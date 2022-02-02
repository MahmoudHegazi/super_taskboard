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
        $statement = $pdo->prepare('INSERT INTO calendar(title, start_year, added_years, periods_per_day, slots_per_period, description, used) VALUES(:title, :start_year, :added_years, :periods_per_day, :slots_per_period, :description, :used)');
        $statement->execute(array(
            'title' => $calendar->get_title(),
            'start_year' => $calendar->get_start_year(),
            'added_years' => $calendar->get_added_years(),
            'periods_per_day' => $calendar->get_periods_per_day(),
            'slots_per_period' => $calendar->get_slots_per_period(),
            'description' => $calendar->get_description(),
            'used' => $calendar->get_used()
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
        'description' => $calendar->get_description(),
        'background' => $calendar->get_description()
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

    // customized to handle limit and offset pagenation with 1 method call
    // with other abilty get if any calendar or not with good performance
    function read_all($limit=0, $offset=0){
      $pdo = $this->getPDO();

      $stmt_string;
      $stmt;
      if ($offset != False && $limit != False && is_numeric($offset) && is_numeric($limit)){
        $stmt_string = "SELECT * FROM calendar LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($stmt_string);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);

      } else if ($offset == False && $limit != False && is_numeric($limit)){
        $stmt_string = "SELECT * FROM calendar LIMIT :limit";
        $stmt = $pdo->prepare($stmt_string);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);

      } else {
        $stmt_string = "SELECT * FROM calendar";
        $stmt = $pdo->prepare($stmt_string);
      }

      $stmt->execute();
      $data = $stmt->fetchAll();
      return $data;
    }

    function delete_all(){
      $pdo = $this->getPDO();
      $statement = $pdo->prepare('DELETE FROM calendar WHERE id > 0');
      return $statement->execute();
    }

    function update_column($column, $value, $id){
      $pdo = $this->getPDO();
      $sql = "UPDATE calendar SET ".$column."=? WHERE id=?";
      $stmt= $pdo->prepare($sql);
      return $stmt->execute([$value, $id]);
    }

    function get_total_calendars(){
      $pdo = $this->getPDO();
      return $pdo->query('select count(id) from calendar')->fetchColumn();
    }

    function upadate_where($column, $value, $new_value){
      $pdo = $this->getPDO();
      $sql = "UPDATE calendar SET ".$column."=? WHERE ".$column."=?";
      $stmt= $pdo->prepare($sql);
      return $stmt->execute([$new_value, $value]);
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
