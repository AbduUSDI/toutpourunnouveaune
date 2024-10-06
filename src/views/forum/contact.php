<?php
session_start();
require '../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $subject && $message) {
        $mail = new PHPMailer(true);

        try {
            // Configuration du serveur SMTP de Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'abdu.usdi@gmail.com'; // Remplacez par votre adresse email Gmail
            $mail->Password = 'Abdufufu2525+';    // Remplacez par votre mot de passe Gmail ou par un mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configuration des destinataires
            $mail->setFrom($email, $name);
            $mail->addAddress('abdu.usdi@gmail.com'); // Remplacez par l'adresse email de destination

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($message);
            $mail->AltBody = $message;

            $mail->send();
            $_SESSION['message'] = "Votre message a été envoyé avec succès.";
            $_SESSION['message_type'] = "success";
        } catch (Exception $e) {
            $_SESSION['message'] = "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
        $_SESSION['message_type'] = "danger";
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

include 'templates/header.php';
include 'templates/navbar_forum.php';
?>

    <div class="container mt-5">
        <h1 class="text-center">Nous contacter</h1>

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
                    ' . htmlspecialchars($_SESSION['message']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Nom</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Sujet</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-info">Envoyer</button>
        </form>
    </div>

</body>
</html>

<?php include 'templates/footer.php'; ?>
