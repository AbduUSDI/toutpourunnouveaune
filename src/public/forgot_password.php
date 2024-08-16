<?php
require_once '../../config/Database.php';
require_once '../models/UserModel.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Assurez-vous que PHPMailer est installé via Composer

$database = new Database();
$db = $database->connect();
$user = new User2($db);
$user2 = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['forgotEmail'];
    $userData = $user2->getUtilisateurParEmail($email);

    if ($userData) {
        $token = bin2hex(random_bytes(32));
        $user->setResetToken($userData['id'], $token);

        $resetLink = "http://localhost/toutpourunnouveaune/src/public/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp-mail.outlook.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'Karausdi77@outlook.fr';
            $mail->Password   = 'Abdufufu2525+';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('Karausdi77@outlook.fr', 'Lien de réinitialisation de mot de passe - TPUNN');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe';
            $mail->Body    = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'Un email de réinitialisation a été envoyé à votre adresse.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "L'email n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Aucun utilisateur trouvé avec cette adresse email."]);
    }
}