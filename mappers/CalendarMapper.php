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

    public function insert($calendar) {
            $statement = $pdo->prepare('INSERT INTO signup(name) VALUES(:name)');

            $statement->execute(array(
                'name' => $name,
            ));
    }

    function read_one(){

    }

    function update($calendarModal){

    }

    function delete($calendar_id){
      // construct the delete statement
      $sql = 'DELETE FROM calendar
              WHERE id = :id';

      // prepare the statement for execution
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':id', $calendar_id, PDO::PARAM_INT);

      // execute the statement
      if ($statement->execute()) {
      	return True;
      } else {
        return False;
      }
    }

    /* actions methods */
    function read_all(){

    }

    function read_list($list_of_ids){

    }

    function add_list($list_of_calendars){
      $added = 0;
      for ($i=0; i<count($list_of_calendars); $i++){
        $this->insert($list_of_calendars[$i]);
        $added += 1;
      }
      return $added;
    }

    function delete_all($list_of_ids){

    }

    function delete_list(){
      $deleted = 0;
      for ($i=0; i<count($list_of_ids); $i++){

        if ($this->delete($list_of_ids[$i])){
          $deleted += 1;
        }
      }

      return array(
        'success'=>$deleted > 0,
        'total_deleted'=>$deleted,
        'total_requested'=>count($list_of_ids)
      );
    }

}


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
