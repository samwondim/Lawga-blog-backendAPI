<?php
class PostModel
{
    private string $title;
    private string $content;
    private string $id;
    private string $img_url;
    private string $author_id;
    private string $category_id;

    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll(): array
    {
        $sql = "SELECT category.category_name,user.username, post.post_id, post.title, post.content, post.created_date
                FROM user JOIN post JOIN category ON post.author_id = user.id and post.category_id = category.category_id;";
        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    public function create(): array
    {
        try {

            if (!isset($_POST['submit']) && !empty($_POST)) {
                $errors = $this->validate_inputs();

                if (isset($_FILES['post_img']) && $_FILES['post_img']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['post_img'];
                    $upload_directory = 'C:/xampp/htdocs/law/images/posts/';

                    $file_name = uniqid() . '_' . $file['name'];

                    if (move_uploaded_file($file['tmp_name'], $upload_directory . $file_name)) {
                        $this->img_url = $upload_directory . $file_name;
                    }
                }

                if (empty($errors)) {
                    $this->title = $_POST['title'];
                    $this->content = $_POST['content'];
                    $this->author_id = $_POST['author_id'];
                    $this->category_id = $_POST['category_id'];

                    $sql = "INSERT INTO post(title, content, author_id, category_id, post_img)
                    VALUES(:title, :content, :author_id, :category_id, :img_url);";
                    $stmt = $this->conn->prepare($sql);

                    $stmt->bindValue(":title", $this->title, PDO::PARAM_STR);
                    $stmt->bindValue(":content", $this->content, PDO::PARAM_STR);
                    $stmt->bindValue(":author_id", $this->author_id, PDO::PARAM_INT);
                    $stmt->bindValue(":category_id", $this->category_id, PDO::PARAM_INT);
                    $stmt->bindValue(":img_url", $this->img_url, PDO::PARAM_STR);

                    $stmt->execute();

                    return ['message' => 'post created succesfully'];
                } else {
                    return $errors;
                }
            }
        } catch (Error $e) {
            echo 'error ' . $e;
        }
    }

    public function get(): array | false
    {
        $id = $_GET['id'];
        $sql = "SELECT * 
                FROM post WHERE post_id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function update(): array
    {
        try {
            $this->title = $_POST['title'];
            $this->content = $_POST['content'];
            $this->id = $_POST['post_id'];

            if (isset($_FILES['post_img']) && $_FILES['post_img']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['post_img'];
                $upload_directory = 'C:/xampp/htdocs/law/images/posts/';

                $file_name = uniqid() . '_' . $file['name'];

                if (move_uploaded_file($file['tmp_name'], $upload_directory . $file_name)) {
                    $this->img_url = $upload_directory . $file_name;
                }
            }

            $sql_fetch = 'SELECT * FROM post
                        WHERE post_id = :id';

            $fetch_stmt = $this->conn->prepare($sql_fetch);
            $fetch_stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            $fetch_stmt->execute();

            $current = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

            $sql = "UPDATE post
                    SET title = :title,
                    content = :content
                    WHERE post_id = :id
                    ";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":title", $this->title ?? $current["title"], PDO::PARAM_STR);
            $stmt->bindValue(":content", $this->content ?? $current["content"], PDO::PARAM_STR);
            $stmt->bindValue(":content", $this->img_url ?? $current["img_url"], PDO::PARAM_STR);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['message' => 'post data updated!'];
            } else  return ['message' => 'error finding requested data!'];
        } catch (Error $e) {
            echo $e;
        }
    }

    public function delete(): array
    {

        $id = $_POST['id'];
        $sql_exists = 'SELECT * FROM post
                        WHERE post_id = :id';

        $c_stmt = $this->conn->prepare($sql_exists);
        $c_stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $c_stmt->execute();
        $data = $c_stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($data)) {
            return ['message' => 'post does not exist'];
        } else {
            $sql = "DELETE FROM post
                WHERE post_id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['message' => 'post deleted!'];
            }
        }
    }

    public function validate_inputs(): array
    {
        $errors = [];

        if (empty($_POST['title'])) {
            $errors[] = 'title field must not be empty!';
        }

        if (empty($_POST['content'])) {
            $errors[] = 'content field must not be empty';
        }

        return $errors;
    }
}
