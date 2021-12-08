<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://bootswatch.com/5/slate/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <title>Hello, world!</title>
    <?php
    $entriesPerRow = 0;
    $headEntries = [];
    $filename = "data.csv";
    $tmpFilename = "./tmp/updated.csv";
    $index = 0;
    ?>
</head>
<?php
function countHeads()
{
    global $entriesPerRow;
    global $headEntries;
    global $filename;
    global $index;

    //$index = $_GET["index"];

    if (($handle = fopen($filename, "r")) !== FALSE) {
        $data = fgetcsv($handle, 1000, ";");
        $entriesPerRow = count($data);

        for ($c = 0; $c < $entriesPerRow; $c++) {
            $headEntries[$c] = $data[$c];
        }
    }
    fclose($handle);
}

function createInputFields()
{
    global $entriesPerRow;
    global $headEntries;

    countHeads();

    for ($e = 0; $e < $entriesPerRow; $e++) {   
        $inputText = "<input type=\"text\" name=\"$headEntries[$e]\" placeholder=\"$headEntries[$e]\" style=\"margin-right:50px\"></input>";
        echo $inputText;
    }
}

function persistInputInCSV()
{
    global $entriesPerRow;
    global $headEntries;
    global $filename;

    $line = [];
    for ($c = 0; $c < $entriesPerRow; $c++) {
        if (!$_POST[$headEntries[$c]]) {
            $line[$c] = "-";
        } else {
            $line[$c] = $_POST[$headEntries[$c]];
        }
    }

    $handle = fopen($filename, "a");
    fputcsv($handle, $line, ";");
    fclose($handle);
}


function updateRow()
{
    global $filename;
    global $tmpFilename;
    global $entriesPerRow;
    global $headEntries;
    $index = $_GET["index"];

    $handle = fopen($filename, "r");
    $currentIndex = 0;

    // Copy all unaffected rows over to a new file
    // Replace selected row with Form values

    if (($handle = fopen($filename, "r")) !== FALSE) {
        if (($handle2 = fopen($tmpFilename, "w")) !== FALSE) {

            // Tableheads
            $data = fgetcsv($handle, 1000, ";");
            fputcsv($handle2, $data, ";");

            // Read Rows from Tablebody
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                // Replace selected line with Post Parameters from Form
                if ($currentIndex == $index) {

                    for ($c = 0; $c < $entriesPerRow; $c++) {
                        if (!$_POST[$headEntries[$c]]) {
                            $data[$c] = "-";
                        } else {
                            $data[$c] = $_POST[$headEntries[$c]];
                        }
                    }
                }
                $currentIndex++;
                // Write normal line 
                fputcsv($handle2, $data, ";");
            }
            fclose($handle2);
        }
        fclose($handle);
    }
}

function updateOriginalFile()
{
    global $filename;
    global $tmpFilename;


    if (($handle = fopen($tmpFilename, "r")) !== FALSE) {
        if (($handle2 = fopen($filename, "w")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                fputcsv($handle2, $data, ";");
            }
            fclose($handle2);
        }
        fclose($handle);
    }
}

function redirectToTable()
{
    if (isset($_POST['addButton'])) {
        global $entriesPerRow;
        global $headEntries;

        for ($e = 0; $e < $entriesPerRow; $e++) {
            echo $_POST[$headEntries[$e]] . "<br>";
        }
        persistInputInCSV();
        header('Location: ' . "index.php");
    }

    if (isset($_POST['updateButton'])) {
        updateRow();
        updateOriginalFile();
        header('Location: ' . "index.php");
    }

    if (isset($_POST['cancelButton'])) {
        header('Location: ' . "index.php");
    }
}

?>

<body>
    <div class="container" style="margin-top: 50px;">
        <?php
        if (!isset($_GET["index"])) {
            $buttonText = "Add Entry";
            $buttonValue = "addButton";
            $headlineText = "Neuen Eintrag hinzufügen";
        } else {
            $buttonText = "Update";
            $buttonValue = "updateButton";
            $headlineText = "Bestehenden Eintrag modifizieren";
        }
        echo "<h1>$headlineText</h1>";
        ?>

        <div class="input">
            <form method="post">
                <?php
                createInputFields();
                global $index;


                echo "<button type=\"submit\" class=\"btn btn-success\" value=$buttonValue name=$buttonValue>$buttonText</button>"

                ?>
                <!-- <button type="submit" class="btn btn-success" value="addButton" name="addButton"><?php echo $buttonText ?></button> -->
                <button type="submit" class="btn btn-danger" value="cancelButton" name="cancelButton">Cancel</button>
            </form>
        </div>
        <?php redirectToTable() ?>
    </div>
</body>

</html>