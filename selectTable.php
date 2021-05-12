<?php

require_once("include/CApp.php");

$form = $app->getForm();

$app->renderHeader("Bordsval"); 

$selectedTime = $_GET["dateAndTime"];

if(!empty($_POST))
{
    $name = $_POST["name"];
    $number = $_POST["number"];
    $email = $_POST["email"];
    $table = $_POST["table"];
    $amount = $_POST["people"];

    $query = "SELECT `available` FROM `tables` WHERE id = $table";
    $result = $app->getdb()->query($query);
    $data = $result->fetch_assoc();

    if($data["available"] == 0)
    {
        echo("Bordet är redan bokat");
    }
    else 
    {
        $query = "INSERT INTO booking (`unix timestamp`, namn, nummer, email, bord) 
        VALUES ('$selectedTime', '$name', '$number', '$email', '$table')";
        $app->getdb()->query($query);
        
        $query = "UPDATE `tables` SET `available`= 0 WHERE id = $table";
        $app->getdb()->query($query);
    }
}

$query = "SELECT `unix timestamp`, bord FROM booking WHERE 1";
$bookingInfo = $app->getdb()->query($query);
$timeAndTable = $bookingInfo->fetch_assoc();

$query = "SELECT id, available FROM tables WHERE 1";
$result = $app->getdb()->query($query);
$tableInfo = $result->fetch_assoc();

if($bookingInfo->num_rows > 0)
{
    for($i = 0; $i < $bookingInfo->num_rows; $i++)
    {
        if($selectedTime > ($timeAndTable["unix timestamp"] - 7200) && $selectedTime < ($timeAndTable["unix timestamp"] + 7200))
        {
            if($timeAndTable["bord"] == $tableInfo["id"])
            {
                $color = "rgb(255,50,50)";
            }
            else
            {
                $color = "rgb(50,255,50)";
            }
        }
    }
}

$form->openDiv("personalInfo");
$form->openDiv("mapMarkers");
for($i = 1; $i <= 15; $i++)
{   
    if($i == 1 || $i == 2)
    {
        $peoplePerTable = $i ."</br>" . "6p";
    }
    else if($i == 3 || $i == 4 || $i == 5 || $i == 6 || $i == 8 || $i == 9)
    {
        $peoplePerTable = $i ."</br>" . "4p";
    }
    else if($i == 7 || $i == 10 || $i == 11 || $i == 12 || $i == 14 || $i == 15)
    {
        $peoplePerTable = $i ."</br>" . "2p";
    }
    if($i == 13)
    {
        $peoplePerTable = $i ."</br>" . "5p";
    }

    echo('<div class="marker table' . $i . '" style="background-color:' . $color . '">bord ' . $peoplePerTable . '</div>');
}
$form->closeDiv();
$form->openForm();
$form->createInput("text", "name", "För/Efternamn");
$form->createInputTel("number", "Telefonnummer");
$form->createInput("email", "email", "E-Mail");
$form->createInputNumber("table", "Bord", "1", "15");
$form->createInputNumber("people", "Antal folk", "1", "6");
$form->createSubmit("Boka bord");
$form->closeForm();
$form->closeDiv();

$app->renderFooter(); 

?>