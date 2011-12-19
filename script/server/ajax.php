<?php

require_once('connect.php');

// INSERT new user content
if($_GET['action'] == 'new')
{
    // SEARCH FOR REFERENCE
    try
    {
        $sql = file_get_contents('select_reference.sql');
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':start_book_id', $_GET['start_book_id']);
        $stmt->bindParam(':start_chapter', $_GET['start_chapter']);
        $stmt->bindParam(':start_verse', $_GET['start_verse']);
        $stmt->bindParam(':end_book_id', $_GET['end_book_id']);
        $stmt->bindParam(':end_chapter', $_GET['end_chapter']);
        $stmt->bindParam(':end_verse', $_GET['end_verse']);

        $stmt->execute();

        $reference_id = $stmt->fetch();
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }

    // INSERT REFERENCE IF NEEDED
    if(empty($reference_id))
    {
        try
        {
            $sql = file_get_contents('insert_reference.sql');
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':start_book_id', $_GET['start_book_id']);
            $stmt->bindParam(':start_chapter', $_GET['start_chapter']);
            $stmt->bindParam(':start_verse', $_GET['start_verse']);
            $stmt->bindParam(':end_book_id', $_GET['end_book_id']);
            $stmt->bindParam(':end_chapter', $_GET['end_chapter']);
            $stmt->bindParam(':end_verse', $_GET['end_verse']);

            $stmt->execute();

            $reference_id = $stmt->fetch();
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    $reference_id = $reference_id[0];

    print_r($reference_id);
}


// GET user content
if($_GET['action'] == 'get')
{
    try
    {
        $sql = file_get_contents('select.sql');
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':book_id', $_GET['book_id']);
        $stmt->bindParam(':chapter', $_GET['chapter']);
        $stmt->execute();
        $content = $stmt->fetchAll();

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<payload>';
        foreach($content as $row)
        {
            echo '<'.$row['content_type'].'>';
                echo '<startBookId>'.$row['start_book_id'].'</startBookId>';
                echo '<endBookId>'.$row['end_book_id'].'</endBookId>';
                echo '<startChapter>'.$row['start_chapter'].'</startChapter>';
                echo '<endChapter>'.$row['end_chapter'].'</endChapter>';
                echo '<startVerse>'.$row['start_verse'].'</startVerse>';
                echo '<endVerse>'.$row['end_verse'].'</endVerse>';
                echo '<content>'.$row['content'].'</content>';
            echo '</'.$row['content_type'].'>';
        }
        echo '</payload>';
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }
}

$db = null;

?>