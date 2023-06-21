<?php
class UserModel
{
    private string $username;
    private string $password;
    private string $email;
    private string $id;
    private string $firstname;
    private string $lastname;
    private string $img_url;

    private PDO $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM user";
        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    public function create()
    {
        try {

            if (!isset($_POST['submit']) && !empty($_POST)) {
                $errors = $this->validate_inputs();

                if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['user_img'];
                    $upload_dir = 'C:/xampp/htdocs/law/images/users/';

                    $file_name = uniqid() . '_' . $file['name'];

                    if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
                        $this->img_url = $upload_dir . $file_name;
                    }
                }

                if (empty($errors)) {
                    $this->firstname = $_POST['firstname'];
                    $this->lastname = $_POST['lastname'];
                    $this->email = $_POST['email'];
                    $this->password = $_POST['password'];
                    $this->username = $_POST['username'];
                    $this->img_url = '';

                    $sql = "INSERT INTO user(firstname,lastname,email,password,username,user_img)
                    VALUES(:firstname,:lastname,:email,:password,:username,:user_img)";

                    $stmt = $this->conn->prepare($sql);

                    $stmt->bindValue(":firstname", $this->firstname, PDO::PARAM_STR);
                    $stmt->bindValue(":lastname", $this->lastname, PDO::PARAM_STR);
                    $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                    $stmt->bindValue(":password", $this->password, PDO::PARAM_STR);
                    $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
                    $stmt->bindValue(":user_img", $this->img_url, PDO::PARAM_STR);


                    $stmt->execute();

                    return $this->conn->lastInsertId();
                } else {
                    return $errors;
                }
            }
        } catch (Error $e) {
            return 'error ' . $e;
        }
    }

    public function get(): array | false
    {
        try {
            $form_data = array(
                'username' => $_POST['username'],
                'password' => $_POST['password']
            );

            $this->username = $form_data['username'];
            $this->password = $form_data['password'];

            $sql = "SELECT * 
                    FROM user
                    WHERE username = :username and password = :password";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->bindValue(":password", $this->password, PDO::PARAM_STR);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            return $data;
        } catch (Error $e) {
            echo 'error' . $e;
            return false;
        }
    }

    public function update(): array | false
    {
        try {
            $this->firstname = $_POST['firstname'];
            $this->lastname = $_POST['lastname'];
            $this->email = $_POST['email'];
            $this->password = $_POST['password'];
            $this->username = $_POST['username'];

            $sql_get_single = "SELECT * FROM user
                            WHERE username= :username";

            $get_stmt = $this->conn->prepare($sql_get_single);

            $get_stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $get_stmt->execute();

            $current = $get_stmt->fetch(PDO::FETCH_ASSOC);

            $sql = "UPDATE user 
                SET firstname = :firstname, 
                lastname= :lastname,
                email = :email,
                password = :password
                WHERE username = :username";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":firstname", $this->firstname ?? $current["firstname"], PDO::PARAM_STR);
            $stmt->bindValue(":lastname", $this->lastname ?? $current["lastname"], PDO::PARAM_STR);
            $stmt->bindValue(":email", $this->email ?? $current["email"], PDO::PARAM_STR);
            $stmt->bindValue(":password", $this->password ?? $current["password"], PDO::PARAM_STR);
            $stmt->bindValue(":username", $this->username ?? $current["username"], PDO::PARAM_STR);

            $stmt->execute();

            return [
                "message" => "data update successful!",
                "rows affected" => "{$stmt->rowCount()} rows affected."
            ];
        } catch (Error $e) {
            echo json_encode(['message' => $e]);
        }
    }

    public function delete()
    {
        $this->username = $_POST['username'];

        $sql = "DELETE FROM user
                WHERE username = :username";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->rowCount();

        if ($res != 0) {
            return $stmt->rowCount();
        } else {
            return ['message' => "invalid username $this->username!"];
        }
    }

    public function validate_inputs(): array
    {
        $errors = [];

        if (empty($_POST['firstname'])) {
            $errors[] = 'firstname field must not be empty!';
        }

        if (empty($_POST['lastname'])) {
            $errors[] = 'lastname field must not be empty';
        }

        if (empty($_POST['email'])) {
            $errors[] = 'email field must not be empty';
        }

        if (empty($_POST['password'])) {
            $errors[] = 'password field must not be empty';
        }

        if (empty($_POST['username'])) {
            $errors[] = 'username field must not be empty';
        }

        return $errors;
    }
}
