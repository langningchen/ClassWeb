<?php
require_once "Function.php";

echo "<html>";
echo "<body style=\"padding: 10%;\">";
echo "<script>";
echo "    setInterval(function() {";
echo "        window.scroll(0, 0x7FFFFFFF);";
echo "    }, 100);";
echo "</script>";
echo "Dropping database... ";
FlushOutput();
$Database->query("DROP DATABASE IF EXISTS " . $DataBaseName);
echo "<br />Creating database... ";
FlushOutput();
$Database->query("CREATE DATABASE " . $DataBaseName);
$Database->query("USE " . $DataBaseName);
echo "<br />Creating table ChatList... ";
FlushOutput();
$Database->query("CREATE TABLE ChatList                     (
        ID                         int(11)   NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        SendUID                    int(11)   NOT NULL                               ,
        ReceiveUID                 int(11)   NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        SendTime                   timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        TheOther                   int(11)            DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ClassFileList... ";
FlushOutput();
$Database->query("CREATE TABLE ClassFileList                (
        ClassFileID                int(11)   NOT NULL                               ,
        ClassID                    int(11)   NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        FileID                     int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table HomeworkUploadCheckList... ";
FlushOutput();
$Database->query("CREATE TABLE HomeworkUploadCheckList (
        HomeworkUploadCheckID int(11)   NOT NULL                               ,
        HomeworkUploadID      int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text               DEFAULT NULL                  ,
        FileName                   text               DEFAULT NULL                  ,
        CheckTime                  timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table HomeworkUploadFileList... ";
FlushOutput();
$Database->query("CREATE TABLE HomeworkUploadFileList  (
        HomeworkUploadFileID  int(11)   NOT NULL                               ,
        HomeworkID            int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        FileName                   text      NOT NULL                               ,
        FileType                   text      NOT NULL                               ,
        FileSize                   text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table HomeworkUploadList... ";
FlushOutput();
$Database->query("CREATE TABLE HomeworkUploadList      (
        HomeworkUploadID      int(11)   NOT NULL                               ,
        HomeworkID                 int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text               DEFAULT NULL                  ,
        FileList                   text               DEFAULT NULL                  ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        Status                     int(11)   NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table HomeworkList... ";
FlushOutput();
$Database->query("CREATE TABLE HomeworkList            (
        HomeworkID            int(11)   NOT NULL                               ,
        ClassID                    int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Title                      text      NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        CreateTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        EndTime                    datetime  NOT NULL                               ,
        NeedUpload                 int(11)   NOT NULL                               ,
        CanUploadAfterEnd          int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ClassList... ";
FlushOutput();
$Database->query("CREATE TABLE ClassList                    (
        ClassID                    int(11)   NOT NULL                               ,
        ClassName                  text      NOT NULL                               ,
        ClassAdmin                 int(11)   NOT NULL                               ,
        ClassTeacher               text      NOT NULL                               ,
        ClassMember                text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ClockInList... ";
FlushOutput();
$Database->query("CREATE TABLE ClockInList                  (
        ClockInID                  int(11)   NOT NULL                               ,
        ClassID                    int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Title                      text      NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        CreateTime                 date      NOT NULL DEFAULT '2000-01-01',
        EndTime                    date      NOT NULL DEFAULT '2000-01-01',
        CanUploadAfterEnd          int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ClockInUploadCheckList... ";
FlushOutput();
$Database->query("CREATE TABLE ClockInUploadCheckList       (
        ClockInUploadCheckID       int(11)   NOT NULL                               ,
        ClockInUploadID            int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        CheckTime                  timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ClockInUploadFileList... ";
FlushOutput();
$Database->query("CREATE TABLE ClockInUploadFileList        (
        ClockInUploadFileID        int(11)   NOT NULL                               ,
        ClockInID                  int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
        FileName                   text      NOT NULL                               ,
        FileType                   text      NOT NULL                               ,
        FileSize                   text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ClockInUploadList... ";
FlushOutput();
$Database->query("CREATE TABLE ClockInUploadList            (
        ClockInUploadID            int(11)   NOT NULL                               ,
        ClockInID                  int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        Data                       text               DEFAULT NULL                  ,
        FileList                   text               DEFAULT NULL                  ,
        UploadDate                 date      NOT NULL                               ,
        UploadTime                 time      NOT NULL DEFAULT '00:00:00'            ,
        Status                     int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table FileList... ";
FlushOutput();
$Database->query("CREATE TABLE FileList                     (
        ID                         int(11)   NOT NULL                               ,
        uploaduid                  int(11)   NOT NULL                               ,
        filename                   text      NOT NULL                               ,
        filetype                   text      NOT NULL                               ,
        filesize                   int(11)   NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table Notice... ";
FlushOutput();
$Database->query("CREATE TABLE Notice                       (
        NoticeID                   int(11)   NOT NULL                               ,
        UploadUID                  int(11)   NOT NULL                               ,
        UploadTime                 timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        Title                      text      NOT NULL                               ,
        Data                       text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table PageCount... ";
FlushOutput();
$Database->query("CREATE TABLE PageCount                    (
        PageCountID                int(11)   NOT NULL                               ,
        URI                        text      NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        IP                         text      NOT NULL                               ,
        Time                       timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table TempPassword... ";
FlushOutput();
$Database->query("CREATE TABLE TempPassword                 (
        UserName                   text      NOT NULL                               ,
        Password                   text      NOT NULL                               ,
        Number                     text      NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table UserList... ";
FlushOutput();
$Database->query("CREATE TABLE UserList                     (
        UID                        int(11)   NOT NULL                               ,
        UserName                   text      NOT NULL                               ,
        Password                   text      NOT NULL                               ,
        UserType                   int(1)    NOT NULL                               ,
        UserEmail                  text               DEFAULT 'N/A'                 ,
        LastLoginTime              timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table NewMessageList... ";
FlushOutput();
$Database->query("CREATE TABLE NewMessageList               (
        NewMessageID               int(11)   NOT NULL                               ,
        UID                        int(11)   NOT NULL                               ,
        Data                       text      NOT NULL                               ,
        URL                        text      NOT NULL                               ,
        Time                       timestamp NOT NULL DEFAULT '2000-01-01 00:00:00'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");
echo "<br />Creating table ErrorLog... ";
FlushOutput();
$Database->query("CREATE TABLE ErrorLog                     (
        ErrorLogID                 int(11)   NOT NULL                               ,
        ErrorType                  text      NOT NULL                               ,
        ErrorString                text      NOT NULL                               ,
        ErrorFile                  text      NOT NULL                               ,
        ErrorLine                  int(11)   NOT NULL                               ,
        ErrorContext               text      NOT NULL                               ,
        ErrorTime                  timestamp NOT NULL DEFAULT '2000-01-01 00:00:00' ,
        ErrorUID                   int(11)            DEFAULT NULL                  ,
        ErrorIP                    text      NOT NULL                               ,
        ErrorURI                   text      NOT NULL                               
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ");

echo "<br />Creating key in table ChatList... ";
FlushOutput();
$Database->query("ALTER TABLE ChatList                ADD PRIMARY KEY (ID                   ), ADD UNIQUE KEY ID                    (ID                   ); ");
echo "<br />Creating key in table ClassFileList... ";
FlushOutput();
$Database->query("ALTER TABLE ClassFileList           ADD PRIMARY KEY (ClassFileID          ), ADD UNIQUE KEY ClassFileID           (ClassFileID          ); ");
echo "<br />Creating key in table HomeworkUploadCheckList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkUploadCheckList ADD PRIMARY KEY (HomeworkUploadCheckID), ADD UNIQUE KEY HomeworkUploadCheckID (HomeworkUploadCheckID); ");
echo "<br />Creating key in table HomeworkUploadFileList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkUploadFileList  ADD PRIMARY KEY (HomeworkUploadFileID ), ADD UNIQUE KEY HomeworkUploadFileID  (HomeworkUploadFileID ); ");
echo "<br />Creating key in table HomeworkUploadList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkUploadList      ADD PRIMARY KEY (HomeworkUploadID     ), ADD UNIQUE KEY HomeworkUploadID      (HomeworkUploadID     ); ");
echo "<br />Creating key in table HomeworkList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkList            ADD PRIMARY KEY (HomeworkID           ), ADD UNIQUE KEY HomeworkID            (HomeworkID           ); ");
echo "<br />Creating key in table ClassList... ";
FlushOutput();
$Database->query("ALTER TABLE ClassList               ADD PRIMARY KEY (ClassID              ), ADD UNIQUE KEY ClassID               (ClassID              ); ");
echo "<br />Creating key in table ClockInList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInList             ADD PRIMARY KEY (ClockInID            ), ADD UNIQUE KEY ClockInID             (ClockInID            ); ");
echo "<br />Creating key in table ClockInUploadCheckList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInUploadCheckList  ADD PRIMARY KEY (ClockInUploadCheckID ), ADD UNIQUE KEY ClockInUploadCheckID  (ClockInUploadCheckID ); ");
echo "<br />Creating key in table ClockInUploadFileList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInUploadFileList   ADD PRIMARY KEY (ClockInUploadFileID  ), ADD UNIQUE KEY ClockInUploadFileID   (ClockInUploadFileID  ); ");
echo "<br />Creating key in table ClockInUploadList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInUploadList       ADD PRIMARY KEY (ClockInUploadID      ), ADD UNIQUE KEY ClockInUploadID       (ClockInUploadID      ); ");
echo "<br />Creating key in table FileList... ";
FlushOutput();
$Database->query("ALTER TABLE FileList                ADD PRIMARY KEY (ID                   ), ADD UNIQUE KEY ID                    (ID                   ); ");
echo "<br />Creating key in table PageCount... ";
FlushOutput();
$Database->query("ALTER TABLE PageCount               ADD PRIMARY KEY (PageCountID          ), ADD UNIQUE KEY PageCountID           (PageCountID          ); ");
echo "<br />Creating key in table Notice... ";
FlushOutput();
$Database->query("ALTER TABLE Notice                  ADD PRIMARY KEY (NoticeID             ), ADD UNIQUE KEY NoticeID              (NoticeID             ); ");
echo "<br />Creating key in table UserList... ";
FlushOutput();
$Database->query("ALTER TABLE UserList                ADD PRIMARY KEY (UID                  ), ADD UNIQUE KEY UID                   (UID                  ); ");
echo "<br />Creating key in table NewMessageList... ";
FlushOutput();
$Database->query("ALTER TABLE NewMessageList          ADD PRIMARY KEY (NewMessageID         ), ADD UNIQUE KEY NewMessageID          (NewMessageID         ); ");
echo "<br />Creating key in table ErrorLog... ";
FlushOutput();
$Database->query("ALTER TABLE ErrorLog                ADD PRIMARY KEY (ErrorLogID           ), ADD UNIQUE KEY ErrorLogID            (ErrorLogID           ); ");


echo "<br />Creating auto increment in table ChatList... ";
FlushOutput();
$Database->query("ALTER TABLE ChatList                MODIFY ID                    int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ClassFileList... ";
FlushOutput();
$Database->query("ALTER TABLE ClassFileList           MODIFY ClassFileID           int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table HomeworkUploadCheckList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkUploadCheckList MODIFY HomeworkUploadCheckID int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table HomeworkUploadFileList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkUploadFileList  MODIFY HomeworkUploadFileID  int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table HomeworkUploadList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkUploadList      MODIFY HomeworkUploadID      int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table HomeworkList... ";
FlushOutput();
$Database->query("ALTER TABLE HomeworkList            MODIFY HomeworkID            int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ClassList... ";
FlushOutput();
$Database->query("ALTER TABLE ClassList               MODIFY ClassID               int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ClockInList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInList             MODIFY ClockInID             int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ClockInUploadCheckList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInUploadCheckList  MODIFY ClockInUploadCheckID  int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ClockInUploadFileList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInUploadFileList   MODIFY ClockInUploadFileID   int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ClockInUploadList... ";
FlushOutput();
$Database->query("ALTER TABLE ClockInUploadList       MODIFY ClockInUploadID       int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table FileList... ";
FlushOutput();
$Database->query("ALTER TABLE FileList                MODIFY ID                    int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table PageCount... ";
FlushOutput();
$Database->query("ALTER TABLE PageCount               MODIFY PageCountID           int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table Notice... ";
FlushOutput();
$Database->query("ALTER TABLE Notice                  MODIFY NoticeID              int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table UserList... ";
FlushOutput();
$Database->query("ALTER TABLE UserList                MODIFY UID                   int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table NewMessageList... ";
FlushOutput();
$Database->query("ALTER TABLE NewMessageList          MODIFY NewMessageID          int(11) NOT NULL AUTO_INCREMENT; ");
echo "<br />Creating auto increment in table ErrorLog... ";
FlushOutput();
$Database->query("ALTER TABLE ErrorLog                MODIFY ErrorLogID            int(11) NOT NULL AUTO_INCREMENT; ");

$MailContent = "langningc2009.ml Init log: <br />";
for ($i = 0; $i < count($PreConfigUsers); $i++) {
    echo "<br />Creating " . $PreConfigUsers[$i][0] . " account... ";
    FlushOutput();
    $UserName = $PreConfigUsers[$i][0];
    $UserType = $PreConfigUsers[$i][1];
    $Password = CreateRandPassword();
    $MailContent .= "Username: <pre>" . $PreConfigUsers[$i][0] . "</pre>  Password: <pre>" . SanitizeString($Password) . "</pre><br />";
    $Password = EncodePassword($Password);
    $DatabaseQuery = $Database->prepare("INSERT INTO UserList(UserName, Password, UserType) VALUES (?, ?, ?)");
    $DatabaseQuery->bind_param("ssi", $UserName, $Password, $UserType);
    $DatabaseQuery->execute();
}
$Mailer = InitMailer();
$Mailer->addAddress("langningc2009.ml@outlook.com", "Admin");
$Mailer->Subject = "Init log";
$Mailer->Body = $MailContent;
$Mailer->send();

$ClassMember = "";
for ($Index = 1; $Index <= 50; $Index++) {
    FlushOutput();
    $UserName = "23";
    $UserType = 0;
    $Password = "";
    if ($Index < 10) $UserName .= "0";
    $UserName .= $Index;
    echo "<br />Creating user " . $UserName . "... ";
    FlushOutput();
    $Password = CreateRandPassword();
    $DatabaseQuery = $Database->prepare("INSERT INTO TempPassword(UserName, Password, Number) VALUES (?, ?, ?)");
    $DatabaseQuery->bind_param("sss", $UserName, $Password, $IDNumbers[$Index - 1]);
    $DatabaseQuery->execute();
    $Password = EncodePassword($Password);
    $DatabaseQuery = $Database->prepare("INSERT INTO UserList(UserName, Password, UserType) VALUES (?, ?, ?)");
    $DatabaseQuery->bind_param("ssi", $UserName, $Password, $UserType);
    $DatabaseQuery->execute();
    $ClassMember .= $DatabaseQuery->insert_id . ",";
}
echo "<br />Creating class... ";
FlushOutput();
$ClassName = "建平西校初二23班";
$ClassAdmin = "5";
$ClassTeacher = "6,7";
$ClassMember = substr($ClassMember, 0, strlen($ClassMember) - 1);
$DatabaseQuery = $Database->prepare("INSERT INTO ClassList(ClassName, ClassAdmin, ClassTeacher, ClassMember) VALUES (?,?,?,?)");
$DatabaseQuery->bind_param("ssss", $ClassName, $ClassAdmin, $ClassTeacher, $ClassMember);
$DatabaseQuery->execute();

echo "<br />Extracting files... ";
FlushOutput();
$ZipFile = new \ZipArchive;
$ZipFile->open('Data.zip');
$ZipFile->extractTo('./');
$ZipFile->close();

echo "<br />OK";
echo "</body>";
echo "</html>";
