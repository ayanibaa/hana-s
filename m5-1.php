<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<font size="5" color="purple"><span style="sans-serif">テストがピンチな科目を教えて(パスワードは特定されてもいいもので！)</span></font>
<hr>
<?php
//DB接続設定 
 $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブルの作成
$sql = "CREATE TABLE IF NOT EXISTS m5of1"
    ." ("
    . "id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32) NOT NULL,"
    . "comment TEXT NOT NULL,"
    ."pass char(32) NOT NULL,"
    . "date TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
    .");";
    $stmt = $pdo->query($sql);

//変数の初期化
$name=NULL;
$comment=NULL;
$pass=NULL;
$id=NULL;

//提出ボタンが押された際
if(isset($_POST["teisyutu"])&&!empty($_POST["name"])&&!empty($_POST["comment"])&&empty($_POST["bangou"])&&!empty($_POST["pass"])){
 //POST代入  
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $pass=$_POST["pass"];
//データ入力
    $sql = $pdo -> prepare("INSERT INTO m5of1 (name, comment, pass) VALUES (:name, :comment, :pass)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql -> bindparam(':pass', $pass, PDO::PARAM_STR);
    $sql -> execute();
    echo "<div style='padding: 5px; margin-bottom: 5px; border: 1px solid #333333; border-radius: 5px; background-color: #009999; color: #ffffff;'>投稿完了！</div>";
}


//削除ボタンを押した場合
if(isset($_POST["sakujo"])){
//POST代入
if(!empty($_POST["delete"])){
    $id=$_POST["delete"];
if(!empty($_POST["delpass"])){
    $pass=$_POST["delpass"];

//idとpass
    $sql = 'SELECT * FROM m5of1';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
        
    foreach($results as $result){
     if($result['id']==$id&&$result['pass']==$pass){
        $edname=$result['name'];
        $edcomment=$result['comment'];
        echo "<div style='padding: 5px; margin-bottom: 5px; border: 1px solid #333333; border-radius: 5px; background-color: #009999; color: #ffffff;'>削除完了！</div>";
            }elseif($result['id']==$id&&$result['pass']!=$pass){
                  echo "<font color=red>正しいパスワードを入力してください！</font>";
                  break;
            }}

//レコードから削除
    $sql = 'delete from m5of1 WHERE id=:id AND pass=:pass';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->execute();
    }}}
             
//編集の場合
elseif(isset($_POST["hensyu"])&&!empty($_POST["edit"])&&!empty($_POST["edpass"])){
        $edit=$_POST["edit"];
        $edpass=$_POST["edpass"];
//idがあっていた場合、抽出
         $sql = 'SELECT * FROM m5of1';
         $stmt = $pdo->query($sql);
         $results = $stmt->fetchAll();
        
        foreach($results as $result){
            if($result['id']==$edit&&$result['pass']==$edpass){
                $edname=$result['name'];
                $edcomment=$result['comment'];
                break;
            }elseif($result['id']==$edit&&$result['pass']!=$edpass){
                  echo "<font color=red>正しいパスワードを入力してください！</font>";
                  break;
            }

        }
}
//既存の投稿フォーム内に「いま送信された場合は新規投稿か、編集か（新規登録モード／編集モード）」を判断する情報を追加する】
elseif(!empty($_POST["pass"])&&!empty($_POST["name"]) &&!empty($_POST["comment"])&&!empty($_POST["bangou"])&&isset($_POST["teisyutu"])){
//POST代入
    $id=$_POST["bangou"];
    $pass=$_POST["pass"];
    $name=$_POST["name"];
    $comment=$_POST["comment"];
     $sql = "UPDATE m5of1 SET name=:name,comment=:comment,pass=:pass WHERE id=:id";
     $stmt = $pdo->prepare($sql);
     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
     $stmt->bindParam(':name', $name, PDO::PARAM_STR);
     $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
     $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
     $stmt->execute();
     echo "<div style='padding: 5px; margin-bottom: 5px; border: 1px solid #333333; border-radius: 5px; background-color: #009999; color: #ffffff;'>編集完了！</div>";
}
?>

<!--既存の投稿フォームに、上記で取得した「名前」と「コメント」の内容が既に入っている状態で表示させる-->
<!--フォーム作成-->
<form action="" method="POST"> 
<p>名前:<input type="text" name="name" value="<?php if(isset($_POST["hensyu"])&&!empty($edit)&&!empty($edpass)){
                                             if($result['id']==$edit&&$result['pass']==$edpass){
                                            echo $edname;}}
              ?>">&nbsp;
コメント:<input type="text" name="comment" value="<?php 
                                     if(isset($_POST["hensyu"])&&!empty($edit)&&!empty($edpass)){
                                             if($result['id']==$edit&&$result['pass']==$edpass){
                                     echo $edcomment;}}
         ?>">&nbsp;
 <input type="hidden" name="bangou" value="<?php
 if(isset($_POST["hensyu"])&&!empty($edit)&&!empty($edpass)){
       if($result['id']==$edit&&$result['pass']==$edpass){
      echo $edit;
 }}
 ?>">
 パスワード:<input type="password" name="pass" placeholder="パスワードを作成してください" value="<?php
 if(isset($_POST["hensyu"])&&!empty($edit)&&!empty($edpass)){
       if($result['id']==$edit&&$result['pass']==$edpass){
      echo $edpass;
 }}
 ?>">&nbsp;
 <input type="submit" name="teisyutu" value="投稿"><br></p>
 <hr>
 <!--削除フォーム作成-->
<form action="" method="POST"> 
<p>削除対象番号:<input type= "number" name="delete">&nbsp;
パスワード:<input type="password" name="delpass" placeholder="パスワードを入力してください">&nbsp;
 <input type="submit" name="sakujo" value="削除"></p>
 <hr>
<!--編集フォーム作成-->
<p><form action="" method="POST">
編集対象番号:<input type="number" name="edit">&nbsp;
パスワード:<input type="password" name="edpass" placeholder="パスワードを入力してください">&nbsp;
<input type="submit" name="hensyu" value="編集"></p>

<?php
//レコード表示
 $sql = 'SELECT * FROM m5of1 ' ;
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].'<br>';
        echo $row['name'].'<br>';
        echo $row['comment'].'<br>';
        echo $row['date'].'<br>';   
        echo "<hr>";
}
?>
</form>
</body>
</html>