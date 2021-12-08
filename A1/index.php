<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

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
  ?>


</head>

<?php
function createTable()
{
  global $entriesPerRow;
  global $headEntries;
  global $filename;

  // Open Filereader
  if (!is_file($filename)) {
    createCSVFile();
  }
  if (($handle = fopen($filename, "r")) !== FALSE) {
    echo "<thead>";
    echo "<th scope=\"col\">#</th>";
    $data = fgetcsv($handle, 1000, ";");
    $num = count($data);


    // Read Tableheads
    for ($c = 0; $c < $num; $c++) {
      $tablehead = "<th scope=\"col\">" . $data[$c] . "</th>";
      $headEntries[$c] = $data[$c];
      echo $tablehead;

      $entriesPerRow++;
    }
    echo "<th scope=\"col\">Modify</th>";
    echo "</thead>";
    echo "<tbody>";

    // Count Index to assign a number to a row
    $index = 0;

    // Read Rows from Tablebody
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

      $num = count($data);

      echo "<tr>";
      echo "<td>$index</td>";

      // Read Fields from each row
      for ($c = 0; $c < $num; $c++) {
        $tablehead = "<td>" . $data[$c] . "</td>";
        echo $tablehead;
      }

      echo "<td>";
      echo "<form action=\"form.php?index=$index\" method=\"post\">";
      echo "<button type=\"submit\" class=\"btn btn-warning\" value=\"changeButton\" name=\"changeButton\" style=\"width:100px\">Edit</button>";
      echo "</form>";
      echo "<form method=\"get\">";
      echo "<input type=\"hidden\" name=\"index\" value=\"$index\"/>";
      echo "<button type=\"submit\" class=\"btn btn-danger\" value=\"deleteButton\" name=\"deleteButton\" style=\"width:100px\">Delete</button>";
      echo "</form>";
      echo "</td>";
      echo "</tr>";
      $index++;
    }
    echo "</tbody>";

    // Close Filereader
    fclose($handle);
  }
}

function createCSVFile()
{
  global $filename;

  if (($handle2 = fopen($filename, "w")) !== FALSE) {
    $data[0] = "Name";
    $data[1] = "Alter";
    $data[2] = "Groesse";
    $data[3] = "Abonnenten";
    fputcsv($handle2, $data, ";");

    $data[0] = "Finn";
    $data[1] = "Reinhardt";
    $data[2] = "181";
    $data[3] = "28600";
    fputcsv($handle2, $data, ";");

    $data[0] = "Fabian";
    $data[1] = "Buitkamp";
    $data[2] = "190";
    $data[3] = "12";
    fputcsv($handle2, $data, ";");

    $data[0] = "Safran";
    $data[1] = "Serosan";
    $data[2] = "12";
    $data[3] = "190";
    fputcsv($handle2, $data, ";");


    // Close Filereader
    fclose($handle2);
  }
}

function createInputFields()
{
  global $entriesPerRow;
  global $headEntries;
  for ($e = 0; $e < $entriesPerRow; $e++) {
    $inputText = "<input type=\"text\" name=\"$headEntries[$e]\" placeholder=\"$headEntries[$e]\" style=\"margin-right:50px\"></input>";
    echo $inputText;
  }
}

function addUserInputToCSV()
{

  if (isset($_GET['deleteButton'])) {
    deleteRow();
    updateOriginalFile();
    // Refresh Page
    header("Refresh:0; url=index.php");
  }



  /*  if (isset($_POST['submitButton'])) {
      global $entriesPerRow;
      global $headEntries;
      echo "This is Button1 that is selected";
      

      for ($e = 0; $e < $entriesPerRow; $e++) {
        echo $_POST[$headEntries[$e]]."<br>";
      }
    }  */
}

function deleteRow()
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
          /* echo "Mache nichts bei ".$currentIndex; */
        }else{
          fputcsv($handle2, $data, ";");
        }
        $currentIndex++;
        // Write normal line 
        
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



?>


<body>

  <div class="container">

    <table class="table table-striped" style="margin-top: 25px;">
      <?php
      createTable();
      addUserInputToCSV();
      ?>
    </table>
    <div class="input">
      <form method="post" action="form.php">
        <button type="submit" class="btn btn-success" value="submitButton" name="submitButton" style="float:right">Add Entry</button>
      </form>
    </div>

  </div>
</body>

</html>