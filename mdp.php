<?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "examen";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if($conn->connect_error){
        die("Echec de la connexion : " . $conn->connect_error);
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de Passe Oublié</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        .link {
            margin-top: 15px;
        }

        .link a {
            color: #007bff;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    

        <!-- Formulaire pour entrer un nouveau mot de passe -->
        <div id="resetPasswordSection" style="display: none;">
            <h1>Réinitialiser le mot de passe</h1>
            <form id="resetPasswordForm">
                <div class="form-group">
                    <label for="newPassword">Nouveau mot de passe :</label>
                    <input type="password" id="newpassword" name="newpassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmnewpassword">Confirmer le nouveau mot de passe :</label>
                    <input type="password" id="confirmNewpassword" name="confirmnewpassword" required>
                </div>
                <button type="submit">Réinitialiser le mot de passe</button>
            </form>
            <div class="link">
                <p><a href="connexion.php">Retour à la connexion</a></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const requestResetSection = document.getElementById('requestResetSection');
            const resetPasswordSection = document.getElementById('resetPasswordSection');
            const requestResetForm = document.getElementById('requestResetForm');
            const resetPasswordForm = document.getElementById('resetPasswordForm');

            // Simulation de l'envoi des instructions pour réinitialiser le mot de passe
            requestResetForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const resetEmail = document.getElementById('resetEmail').value;

                if (resetEmail === '') {
                    alert('Veuillez entrer votre adresse e-mail.');
                    return;
                }

                // Simuler l'envoi de l'email avec les instructions
                alert('Instructions pour réinitialiser votre mot de passe ont été envoyées à votre adresse e-mail.');

                // Passer à la section pour entrer un nouveau mot de passe
                requestResetSection.style.display = 'none';
                resetPasswordSection.style.display = 'block';
            });

            // Validation pour la réinitialisation du mot de passe
            resetPasswordForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const newPassword = document.getElementById('newPassword').value;
                const confirmNewPassword = document.getElementById('confirmNewPassword').value;

                if (newPassword === '' || confirmNewPassword === '') {
                    alert('Veuillez remplir tous les champs.');
                    return;
                }

                if (newPassword !== confirmNewPassword) {
                    alert('Les mots de passe ne correspondent pas.');
                    return;
                }

                alert('Votre mot de passe a été réinitialisé avec succès !');
                // Rediriger l'utilisateur vers la page de connexion
                window.location.href = 'connexion.php';
            });
        });
    </script>
</body>
</html>
