<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$check_stmt = $conn->prepare("SELECT COUNT(*) as total FROM unit_registrations WHERE student_id = ?");
$check_stmt->bind_param("i", $student_id);
$check_stmt->execute();
$check_data = $check_stmt->get_result()->fetch_assoc();

if ($check_data['total'] == 0) {
    header("Location: dashboard.php");
    exit();
}

$verification_code = "VER-" . date("Ymd") . "-" . $student['adm_number'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Scanner & Verification - Smart University</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        #reader {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            padding: 10px;
        }

        #statusBox {
            display: none;
            margin-top: 20px;
        }

        .scanner-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        .scanner-actions .btn {
            width: auto;
            min-width: 220px;
            text-align: center;
        }

        .scanner-note {
            margin-top: 15px;
            font-size: 15px;
            text-align: center;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>SMART UNIVERSITY</h1>
    <h2>Scanner & Unit Verification</h2>

    <div class="card">
        <p><strong>Student:</strong> <?php echo htmlspecialchars($student['fullname']); ?></p>
        <p><strong>Admission Number:</strong> <?php echo htmlspecialchars($student['adm_number']); ?></p>
        <p><strong>Verification Code:</strong> <?php echo htmlspecialchars($verification_code); ?></p>
    </div>

    <div class="card">
        <h3 style="text-align:center;">Live QR Scanner Camera</h3>

        <div id="reader"></div>

        <div class="scanner-actions">
            <button class="btn" onclick="startScanner()">Start Camera Scanner</button>
            <button class="btn" onclick="simulateVerification()">Use Single Device Fallback</button>
        </div>

        <div class="scanner-note">
            Scan a QR code using webcam, or use fallback for one-device presentation/demo.
        </div>

        <div id="statusBox" class="warning">
            Pending verification...
        </div>
    </div>

    <a class="btn" href="dashboard.php">Back to Dashboard</a>
</div>

<script>
let html5QrCode;
let scannerStarted = false;
let verificationInProgress = false;

function startScanner() {
    if (scannerStarted) return;

    html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices[0].id;

            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                qrCodeMessage => {
                    if (!verificationInProgress) {
                        verificationInProgress = true;
                        stopScanner();
                        beginVerification("qr_scan", qrCodeMessage);
                    }
                },
                errorMessage => {
                    // ignore scan errors silently
                }
            ).then(() => {
                scannerStarted = true;
            }).catch(err => {
                alert("Unable to start camera. Please allow camera permission or use Single Device Fallback.");
            });
        } else {
            alert("No camera found. Use Single Device Fallback.");
        }
    }).catch(err => {
        alert("Camera access failed. Please allow permission or use Single Device Fallback.");
    });
}

function stopScanner() {
    if (html5QrCode && scannerStarted) {
        html5QrCode.stop().then(() => {
            scannerStarted = false;
        }).catch(() => {});
    }
}

function simulateVerification() {
    if (verificationInProgress) return;
    verificationInProgress = true;
    stopScanner();
    beginVerification("single_device_fallback", "FALLBACK-<?php echo $student['adm_number']; ?>");
}

function beginVerification(method, payload) {
    const statusBox = document.getElementById("statusBox");
    statusBox.style.display = "block";
    statusBox.className = "warning";
    statusBox.innerHTML = "Pending verification... Please wait 5 seconds.";

    setTimeout(() => {
        const formData = new FormData();
        formData.append("method", method);
        formData.append("payload", payload);

        fetch("verify_registration.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            if (result.trim() === "success") {
                statusBox.className = "success";
                statusBox.innerHTML = "Verification successful! Redirecting to dashboard...";
                setTimeout(() => {
                    window.location.href = "dashboard.php";
                }, 2000);
            } else {
                statusBox.className = "error";
                statusBox.innerHTML = "Verification failed: " + result;
                verificationInProgress = false;
            }
        })
        .catch(() => {
            statusBox.className = "error";
            statusBox.innerHTML = "Network error during verification.";
            verificationInProgress = false;
        });
    }, 5000);
}
</script>
</body>
</html>