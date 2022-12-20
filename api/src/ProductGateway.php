<?php
class ProductGateway
{
  private PDO $conn;
  function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
  }

  /**
   * Undocumented function
   *
   * @return mixed[] | false
   */
  public function getAll()
  {
    $sql = "SELECT * FROM product limit 100";

    $query = $this->conn->query($sql);
    if ($query) {
      return $query->fetchAll(PDO::FETCH_CLASS);
    } else {
      return false;
    }
  }

  /**
   * Undocumented function
   *
   * @return string | false;
   */
  function create()
  {
    $sql = "INSERT INTO product (name, size, is_available)
    VALUES  (:name, :size, :is_available)";

    $stmt = $this->conn->prepare($sql);

    $size = isset($_POST['size']) ? $_POST['size'] : 0;
    $is_available = isset($_POST['is_available']) ? (bool) $_POST['is_available'] : false;

    $stmt->bindValue(":name", $_POST['name'], PDO::PARAM_STR);
    $stmt->bindValue(":size", $size, PDO::PARAM_INT);
    $stmt->bindValue(":is_available", $is_available, PDO::PARAM_BOOL);

    $stmt->execute();

    return $this->conn->lastInsertId();
  }

  /**
   * Undocumented function
   *
   * @param string $id
   * @return mixed|false
   */
  function get($id)
  {
    $sql = "SELECT * from product where id = :id";
    // $sql = "SELECT * FROM product limit 100";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Product');
    return $stmt->fetch();
  }

  /**
   * Undocumented function
   *
   * @param string $current
   * @param mixed[] $patch_data
   * @return number
   */
  function update($id, $patch_data)
  {
    $sql = "UPDATE product set name = :name, size = :size, is_available = :is_available where id = :id";

    $stmt = $this->conn->prepare($sql);

    $size = isset($patch_data['size']) ? $patch_data['size'] : 0;
    $is_available = isset($patch_data['is_available']) ? (bool) $patch_data['is_available'] : false;

    $stmt->bindValue(":name", $patch_data['name'], PDO::PARAM_STR);
    $stmt->bindValue(":size", $size, PDO::PARAM_INT);
    $stmt->bindValue(":is_available", $is_available, PDO::PARAM_BOOL);
    $stmt->bindValue(":id", $id, PDO::PARAM_BOOL);

    $stmt->execute();

    return $stmt->rowCount();
  }

  function delete(string $id): int
  {
    $sql = "DELETE FROM product where id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_BOOL);
    $stmt->execute();
    return $stmt->rowCount();
  }
}
