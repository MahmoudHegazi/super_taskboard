<?php
class CalendarMapper {
    /* Main CRUD */
    /**
      * @var PDO
    */

    protected $pdo;

    public function __construct(PDO $pdo)
    {
      $this->pdo = $pdo;
    }

    public function getPDO(){
      return $this->pdo;
    }


    public function insert($calendar) {
        global $pdo;
        $statement = $pdo->prepare('INSERT INTO calendar(title, start_year, added_years, periods_per_day, slots_per_period, description) VALUES(:title, :start_year, :added_years, :periods_per_day, :slots_per_period, :description)');
        $statement->execute(array(
            'title' => $calendar->get_title(),
            'start_year' => $calendar->get_start_year(),
            'added_years' => $calendar->get_added_years(),
            'periods_per_day' => $calendar->get_periods_per_day(),
            'slots_per_period' => $calendar->get_slots_per_period(),
            'description' => $calendar->get_description()
        ));
        return $pdo->lastInsertId();
    }


    function read_one($calendar_id){
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM calendar WHERE id=:id");
      $stmt->bindParam(':id', $calendar_id, PDO::PARAM_INT);
      $stmt->execute();
      $data = $stmt->fetch();
      return $data;
    }

    function update($calendar){
      $statement = $pdo->prepare('UPDATE calendar (title, start_year, added_years, periods_per_day, slots_per_period, description) VALUES(:title, :start_year, :added_years, :periods_per_day, :slots_per_period, :description)');
      $statement->execute(array(
        'title' => $calendar->get_title(),
        'start_year' => $calendar->get_start_year(),
        'added_years' => $calendar->get_added_years(),
        'periods_per_day' => $calendar->get_periods_per_day(),
        'slots_per_period' => $calendar->get_slots_per_period(),
        'description' => $calendar->get_description()
      ));
    }

    function delete($calendar_id){
      // construct the delete statement
      $pdo = $this->getPDO();
      $sql = 'DELETE FROM calendar
              WHERE id = :id';
      // prepare the statement for execution
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $calendar_id, PDO::PARAM_INT);
      // execute the statement
      if ($statement->execute()){
        return True;
      } else {
        return False;
      }
    }

    function read_all(){
      //$stmt = $pdo->prepare("SELECT * FROM users LIMIT :limit, :offset");
      $pdo = $this->getPDO();
      $stmt = $pdo->prepare("SELECT * FROM calendar");
      $stmt->execute();
      $data = $stmt->fetchAll();
      return $data;
    }

    function delete_all(){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('DELETE FROM calendar WHERE id > 0');
      return $statement->execute();
    }

}

/*
global $pdo;
$calendarMapper = new CalendarMapper($pdo);
var_dump($calendarMapper);
*/
// actual program flow
/*
$pdo = new PDO($dsn, $username, $password);
$signUp = new SignUp($pdo);

if (array_key_exists('id', $_POST)) {
    $signUp->insert($_POST['id']);

    echo 'inserted';
    exit;
}
*/
