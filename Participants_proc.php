<?php
session_start();
require_once('InputValidation/CBaseFormValidation.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/OAuth.php';

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
    <link rel="stylesheet" type="text/css" href="css/estilos_lista.css">
</head>
<body>
<div id='pagina'>
    <div id='cabecalho'>
        <?php include('cabecalho.php'); ?>
    </div>
    <div id='menu'>
        <?php include('menu.php'); ?>
    </div>
    <div id='conteudo'>
        <?php

        $nomeCompleto = CBaseFormValidation::test_input($_POST['NomeCompleto']);
        $curso = CBaseFormValidation::test_input($_POST['Curso']);
        $escola = CBaseFormValidation::test_input($_POST['Escola']);
        $email = CBaseFormValidation::test_input($_POST['Email']);
        $emailRetype = CBaseFormValidation::test_input($_POST['EmailRetype']);
        $telemovel = CBaseFormValidation::test_input($_POST['Telemovel']);
        $ano = CBaseFormValidation::test_input($_POST['Ano']);
        $nivel = "0";
        $numberOfVisits = 0;
        $data_cancelamento = '';
        $data = date("Y/m/d G:i:s", time());
        $referenciaConfirmacao = sha1(date("Y/m/d G:i:s", time()) . $email);
        $data_inscricao = $data;
        $situacaoInscricao = 'NAO CONFIRMADA';

        // validação de dados
        $ERR = 0;
        $V = array();
        if (1 == 1) {

        }

        /* base de dados - ligação */
        //require_once('/var/../db.php');

        $dbName = 'test';
        $dbPass = '';
        $dbUser = 'root';
        $dbHost = 'localhost';
        $dbPort = '3306';
        $dbTableName = 'participantes';

        try {
            $options = array(1002 => 'SET NAMES UTF8');
            $ligacao = new PDO("mysql:host={$dbHost}; dbname={$dbName}; port={$dbPort}", $dbUser, $dbPass, $options);
            $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $pe) {
            echo($pe->getMessage());
        }
        // Criar base de dados
        $dbName = 'jei2017_php_mysql';
        $sql = "CREATE database IF NOT EXISTS $dbName";
        $stmt = $ligacao->prepare($sql);
        $stmt->execute();


        $ligacao->exec("use $dbName");
        $stmt = $ligacao->prepare("delete from $dbTableName;");
        $stmt->execute();

        $sql = "CREATE TABLE IF NOT EXISTS $dbTableName  (
        ID int(6) NOT NULL AUTO_INCREMENT,
        SUBMIT_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        NomeCompleto varchar(60)  NOT NULL,
        Curso varchar(100) NOT NULL,
        Escola varchar(60) NOT NULL,
        Email varchar(40)  NOT NULL,
        EmailRetype varchar(40)  NOT NULL,
        Telemovel varchar(12)  NOT NULL,
        Ano varchar(20)  NOT NULL,

        Nivel varchar(10)  NOT NULL DEFAULT '0',
        NumberOfVisits int(11) DEFAULT '0',
        SituacaoInscricao varchar(30)  NOT NULL DEFAULT 'NAO CONFIRMADA',
        ReferenciaConfirmacao varchar(200)  NOT NULL,
        data_inscricao datetime DEFAULT NULL,
        data_confirmacao datetime DEFAULT NULL,
        data_cancelamento datetime DEFAULT NULL,
        Data datetime DEFAULT NULL,
        PRIMARY KEY (ID)
    );";
        $stmt = $ligacao->prepare($sql);
        $stmt->execute();

        $data = date("Y/m/d G:i:s", time());  // $data = date("Y/m/d G:i:s", time());

        $situacaoInscricao = 'NAO CONFIRMADA';
        $data_inscricao = $data;
        $data_confirmacao = $data;
        $data_cancelamento = $data;
        $nivel = '0';
        $numberOfVisits = '0';

        $res = null;
        try {
            $sql = "INSERT INTO $dbTableName  (NomeCompleto, Curso, Escola, Email, EmailRetype, Telemovel, Ano, Nivel,
					NumberOfVisits, SituacaoInscricao, ReferenciaConfirmacao, data_inscricao, data_confirmacao, data_cancelamento, Data) 
					VALUES (:NomeCompleto, :Curso, :Escola, :Email, :EmailRetype, :Telemovel, :Ano, :Nivel, :NumberOfVisits, :SituacaoInscricao, :ReferenciaConfirmacao, :data_inscricao, :data_confirmacao, :data_cancelamento, :Data)";
            $stmt = $ligacao->prepare($sql);
            $stmt->bindParam(':NomeCompleto', $nomeCompleto);
            $stmt->bindParam(':Curso', $curso);
            $stmt->bindParam(':Escola', $escola);
            $stmt->bindParam(':Email', $email);
            $stmt->bindParam(':EmailRetype', $emailRetype);
            $stmt->bindParam(':Telemovel', $telemovel);
            $stmt->bindParam(':Ano', $ano);
            $stmt->bindParam(':Nivel', $nivel);
            $stmt->bindParam(':NumberOfVisits', $numberOfVisits);
            $stmt->bindParam(':SituacaoInscricao', $situacaoInscricao);
            $stmt->bindParam(':ReferenciaConfirmacao', $referenciaConfirmacao);
            $stmt->bindParam(':data_inscricao', $data_inscricao);
            $stmt->bindParam(':data_confirmacao', $data_confirmacao);
            $stmt->bindParam(':data_cancelamento', $data_cancelamento);
            $stmt->bindParam(':Data', $data);
            $res = $stmt->execute();
        } catch (PDOException $pe) {
            //die($pe->getMessage());
            $res = false;
        }
        if ($res == false) {
            $V[] = "<p>Não foi possível registar a sua inscrição.</p>";
            $ERR++;
        }

        if ($ERR) {
            echo "<h1>A inscrição não foi efetuada.</h1>";
            echo "<h2>Apresenta as seguintes incorreções.</h2>";
            echo "<ul>";
            for ($i = 0; $i < count($V); $i++)
                echo "<li>$V[$i]</li>";
            echo "</ul>";
            echo "<h2>Por favor, preencha os dados corretamente e tente de novo.</h2>";
            echo $msg_voltar;
        }

        //$obj_part->listAllHTML();
        if ($situacaoInscricao == 'CONFIRMADA')
            $cor = "style=background-color:#5CE638;";
        else
            $cor = "style=background-color:#ec971f;";


        $css = "<style>
    table {
        border-collapse: collapse;
        border: solid 1px #45aed6;
        background-color: red;
    }
    th {
        border: solid 1px #45aed6;
        text-align: center;
        color: #64686d;
        padding: 4px;
        background-color: white;
    }

    td {
        border: solid 1px #45aed6;
        text-align: left;
        padding: 4px;
        color: #8b4513;
        background-color: white;
    }

    h1 {
        font-size: 18pt;
        color:  black;
        margin: 0;
        font-variant: small-caps;
        margin-bottom: 10px;
    }

    h2 {
        font-size: 14pt;
        color:  black;
        margin: 0;
        margin-top: 20px;
        font-variant: small-caps;
        margin-bottom: 10px;
    }
</style>";

        $data = <<<_END
        $css
        <body><p>Caro aluno $nomeCompleto,</p>
        <p>Mais uma vez muito obrigado por querer participar no JEI2018.</p>
        <br />
        Deve confirmar a sua inscrição consultado o link: <a href='http://localhost/JEI2018_Workshop/Participants_Confirm.php?ref=$referenciaConfirmacao&sit=ok'>confirmar</a>
        <br />
        <h3>Dados da inscrição</h3>
        <table>
            <tr><td class='form'>Nome completo</td><td> $nomeCompleto</td></tr>
            <tr><td class='form'>Escola</td><td> $escola</td></tr>
            <tr><td class='form'>Ano</td><td>$ano</td></tr>
            <tr><td class='form'>Curso</td><td> $curso</td></tr>
            <tr><td class='form'>Email</td><td> $email</td></tr>
            <tr><td class='form'>EmailRetype</td><td> $emailRetype</td></tr>
            <tr><td class='form'>Telemóvel</td><td> $telemovel</td></tr>
            <tr><td class='form'>Situação inscrição</td><td $cor> $situacaoInscricao</td></tr>
            <tr><td class='form'>Data</td><td> $data</td></tr>
        </table>
        <br />
        <p>Com os melhores cumprimentos,</p>
        <p>Organização.</p></body>
_END;


        echo "<p  style='margin-left: 2em'><a href='Participants_lista.php'><input type='button' class='btn' value='Voltar à página principal' /></a></p>";


        // envia email
        // google security ....
        $mail = new PHPMailer();
        $mail->isSMTP(true); // telling the class to use SMTP
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->SMTPSecure = 'tls';
        $mail->Host = 'tls://smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        //include(base64_decode("L3Zhci9qZWkucGhw"));
        include(base64_decode("QzoveGFtcHAvcGhPNNGwL2RvY3MvWE1MX1V0aWwveC5waHA="));
        //include("/var/jei.php");
        //echo base64_encode("/var/jei.php");
        $s1 = base64_decode($s1);
        $s2 = base64_decode($s2);
        $mail->Username = $s1;
        $mail->Password = $s2;
        // TCP port to connect to
        // $mail->SMTPDebug = 1;
        $mail->setFrom('pnunes@ipg.pt');
        $mail->AddAddress("jei@ipg.pt");
        $mail->AddAddress($email);
        $mail->Subject = utf8_decode("W01 - PHP, MySQL, E-Mail e PDF | Jornadas de Engenharia Informática Campus do Instituto Politécnico da Guarda, dias 17 e 18 de abril de 2018");
        $mail->WordWrap = 70;
        $mail->IsHTML(true);
        $mail->msgHTML($data);

        //Attach an image file
        // $mail->addAttachment($ficheiro_tmp_name, $ficheiro_name);
        //send the message, check for errors
        if (!$mail->send()) {
            echo $data;
            echo "<h1 style='color:red'>Não foi possível registar a inscrição!</h1>";
            echo "<p>Tente de novo.<p>";
            echo "My Mailer Error: " . $mail->ErrorInfo;

        } else {
            echo "<h1 style='color:green'>Inscrição efetuada com sucesso.</h1>";
            echo "<h2>Foi-lhe enviado um E-Mail ($email) com o seguinte conteúdo.</h2>";
            echo $data;
        }
        echo "<a href='http://www.jei.ipg.pt/'>Jornadas de Engenharia Informática</a>";


        unset($_SESSION['nomeCompleto']);
        unset($_SESSION['ano']);
        unset($_SESSION['escola']);
        unset($_SESSION['curso']);
        unset($_SESSION['email']);
        unset($_SESSION['emailRetype']);
        unset($_SESSION['telemovel']);
        $_SESSION = array();
        session_destroy();

        ?>
    </div>
    <div id='rodape'>
        <?php include('rodape.php'); ?>
    </div>
</div>
</html>
