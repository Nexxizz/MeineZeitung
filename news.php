<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€

// to do: change name 'Index' throughout this file
require_once './Page.php';

class news extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks
    private $JSON = false;
    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
	 * @return array An array containing the requested data. 
	 * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data
        $sqlGet = "SELECT * FROM news ORDER BY timestamp DESC";

        $recordSet = $this->db->query($sqlGet);

        if(!$recordSet){
            throw new Exception("Datenbankfehler: ".$this->db->error);
        }

        $result = array();

        $record = $recordSet->fetch_assoc();

        while ($record){
            // Aufgabe 3c
            $timestampGer = $this->getLocalizedDate($record["timestamp"]);
            $record["timestamp"] = $timestampGer;
            $result[] = $record;
            $record = $recordSet->fetch_assoc();
        }

        $recordSet->free();

        return $result;
    }

    protected function generateHTMLView () {
        $this->generatePageHeader("News");
        echo <<< EOT
        <body onload="pollNews()">
        <header>
        <img src="logo.png" height="30em">
        <h1>Meine Zeitung</h1>
        </header>
        <nav>
        <a href="#">Home</a>
        <a href="#">Mediathek</a>
        <a href="#">Politik</a>
        <a href="#">Sport</a>
        </nav>
        <section id="newsSection">
        <h2>News-Ticker</h2>
<!--        <article>-->
<!--        <h3>Headline</h3>-->
<!--        <p>Time</p>-->
<!--        <p>Content</p>-->
<!--        </article>-->
        </section>
        <section>
        <h2>Ihre News</h2>
        <form method="post" accept-charset="utf-8" action="news.php" >
        <input type="text" placeholder="Titel Ihrer News" name="newsTitle" required>
        <br>
        <textarea value="Ihre News" name="newsContent" placeholder="Ihrer News" required></textarea>
        <br>
        <input type="submit" value="Absenden">
        </form>
        </section>
        <footer><p>&copy; Fachbereich Informatik</p></footer>
        </body>
EOT;
        $this->generatePageFooter();
    }

    // Ergänzen Sie hier Ihren Code
    protected function generateJSONView () {
        header("Content-Type: application/json; charset=UTF-8");
        $data = $this->getViewData();
        $serializedData = json_encode($data);
        echo $serializedData;
    }
    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
	 * @return void
     */
    protected function generateView():void
    {
        if($this->JSON === true) {
            $this->generateJSONView();
        }
        else {
            $this->generateHTMLView();
        }
//        $this->generatePageHeader('to do: change headline'); //to do: set optional parameters
        // to do: output view of this page
//        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
	 * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();

        if(isset($_GET["JSON"])){
            if($_GET["JSON"] == 1) {
                $this->JSON = true;
            }
        }

        if(isset($_POST["newsTitle"]) && isset($_POST["newsContent"])) {
            $newsTitle = $this->db->real_escape_string($_POST["newsTitle"]);
            $newsContent = $this->db->real_escape_string($_POST["newsContent"]);

            $sqlSet = "INSERT INTO news(title, text) VALUES('$newsTitle', '$newsContent')";

            $sqlCheck = $this->db->query($sqlSet);

            if(!$sqlCheck){
                throw new Exception("Datenbankfehler".$this->db->error);
            }
        }
        // to do: call processReceivedData() for all members
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
	 * @return void
     */

    protected function getLocalizedDate($date){
        $date = new DateTime($date);
        if
        (strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'],"de-DE")>-1){
            return $date->format("d.m.Y H:i:s");
        } else { // English as default
            return $date->format("Y/m/d H:i:s");
        }
    }

    public static function main():void
    {
        try {
            $page = new news();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
news::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >
