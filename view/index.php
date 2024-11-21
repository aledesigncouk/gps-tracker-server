<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload CSV File</title>
</head>
<body>
    <form action="api/items/upload.php" method="post" enctype="multipart/form-data">
        <label for="csv_file">Upload CSV file:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv">
        <br><br>
        <input type="submit" value="Upload and Extract">
    </form>
</body>
</html>
