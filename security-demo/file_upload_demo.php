<?php


echo "=================================================================\n";
echo "  FILE UPLOAD VALIDATION DEMO - Malicious File Upload\n";
echo "=================================================================\n\n";

/* -----------------------------------------------------------------
 | Set up: create a malicious PHP web shell, but name it "photo.jpg"
 | This simulates an attacker renaming shell.php to shell.jpg to
 | bypass a naive extension-only check.
 ----------------------------------------------------------------- */
$maliciousFile = tempnam(sys_get_temp_dir(), 'demo');
file_put_contents($maliciousFile, '<?php system($_GET["cmd"]); ?>');
$fakeName = 'photo.jpg';   // looks like an image...

echo "Attacker uploads a file named : {$fakeName}\n";
echo "But the real content is       : <?php system(\$_GET[\"cmd\"]); ?>\n";
echo "-----------------------------------------------------------------\n\n";


/* =================================================================
 | BEFORE — UNSAFE VERSION
 | Validates by file EXTENSION only (checks the filename).
 | This is the common mistake that allows web shell uploads.
 ================================================================= */
echo "[ BEFORE ] Unsafe validation (checks file extension only)\n";

$extension = strtolower(pathinfo($fakeName, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png'];

if (in_array($extension, $allowedExtensions, true)) {
    echo "  Result: ACCEPTED  <-- DANGER! The web shell got through\n";
    echo "  Reason: The extension '.jpg' is on the allow-list, so the\n";
    echo "          malicious PHP file was accepted without inspecting it.\n";
} else {
    echo "  Result: REJECTED\n";
}

echo "\n";


/* =================================================================
 | AFTER — SAFE VERSION
 | Validates by reading the REAL file content (magic bytes) using
 | finfo, exactly like the Complaint module's StoreComplaintRequest
 | ('image' + 'mimes' rules perform this check in Laravel).
 ================================================================= */
echo "[ AFTER ]  Safe validation (checks real file content / magic bytes)\n";

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$realMime = $finfo->file($maliciousFile);   // reads actual content, not the name
$allowedMimes = ['image/jpeg', 'image/png'];

echo "  Detected real MIME type: {$realMime}\n";

if (in_array($realMime, $allowedMimes, true)) {
    echo "  Result: ACCEPTED\n";
} else {
    echo "  Result: REJECTED  <-- SAFE! The web shell was blocked\n";
    echo "  Reason: finfo detected the real content is not an image,\n";
    echo "          so the file was rejected despite its .jpg name.\n";
}

echo "\n-----------------------------------------------------------------\n";
echo "CONCLUSION:\n";
echo "  The unsafe version trusts the filename and lets the attack\n";
echo "  through. The safe version inspects the actual file content\n";
echo "  and blocks it. This is the protection implemented in the\n";
echo "  Complaint module via StoreComplaintRequest.\n";
echo "=================================================================\n";

// Clean up
unlink($maliciousFile);