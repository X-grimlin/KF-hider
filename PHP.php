<?php

// Function to hide the answer based on the letter after "R:"
if (!function_exists('hideAnswer')) {
    function hideAnswer($pdf_content, $answer_letter) {
        // Find the last occurrence of "R :" in the PDF content
        $last_r_index = strrpos($pdf_content, "R :");
        if ($last_r_index !== false) {
            // Hide the "R: X" line where X is the answer letter
            $pdf_content = substr_replace($pdf_content, "R : *********", $last_r_index, strlen("R :") + 2);
        }
        return $pdf_content;
    }
}

// Check if a file was uploaded
if(isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['pdfFile']['tmp_name'];

    // Define the path to save the uploaded file
    $upload_path = "uploads/";

    // Move uploaded file to the designated location
    move_uploaded_file($file_tmp, $upload_path . $_FILES['pdfFile']['name']);

    // Read the content of the uploaded PDF file, starting from page 5
    $pdf_content = shell_exec("pdftotext -f 5 " . $upload_path . $_FILES['pdfFile']['name'] . " -");

    if ($pdf_content !== null) {
        // Extract the answer letter (A, B, C, D, etc.) from the "R: X" line
        preg_match("/R : ([A-Z])/", $pdf_content, $matches);
        if (isset($matches[1])) {
            $answer_letter = $matches[1];
            // Hide the answer based on the detected letter
            $pdf_content = hideAnswer($pdf_content, $answer_letter);
        }

        // Output the modified PDF content
        echo nl2br(htmlspecialchars($pdf_content));
    } else {
        echo "<p>Failed to extract text from the PDF file.</p>";
    }
} else {
    echo "<p>No file uploaded.</p>";
}
?>
