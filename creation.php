<?php

session_start();


$servername = "localhost";  
$username = "root";         
$password = "";             
$dbname = "examen";        


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Initialisation des messages
$success_message = "";
$error_message = "";

// Vérifie si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assure que les clés existent dans $_POST avant de les utiliser
    $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $telephone = isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '';
    $mot_de_passe = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';
    $confirm_mot_de_passe = isset($_POST['confirm-password']) ? htmlspecialchars($_POST['confirm-password']) : '';

    // Valide les données (ici, on vérifie que les champs ne sont pas vides et que les mots de passe correspondent)
    if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($mot_de_passe) || empty($confirm_mot_de_passe)) {
        $error_message = "Tous les champs sont requis.";
    } elseif ($mot_de_passe !== $confirm_mot_de_passe) {
        $error_message = "Les mots de passe ne correspondent pas.";
    } else {
        // Hache le mot de passe
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        
        $sql = "INSERT INTO connexion (nom, prenom, email, tel, password) VALUES (?, ?, ?, ?, ?)";

       
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Lie les paramètres
            $stmt->bind_param("sssss", $nom, $prenom, $email, $telephone, $mot_de_passe_hash);

            // Exécute la déclaration
            if ($stmt->execute()) {
                $success_message = "Inscription réussie !";
            } else {
                $error_message = "Erreur lors de l'inscription : " . $stmt->error;
            }

            // Ferme la déclaration
            $stmt->close();
        } else {
            $error_message = "Erreur de préparation de la requête.";
        }
    
        header("Location: connexion.php"); // Redirection vers la page d'accueil
        exit();
    }



    // Ferme la connexion
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Inscription</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f5f7f9;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 110vh;
        margin: 0;
    }

    .container {
        background-color: #ffffff;
        padding: 35px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 80%;
    }

    h1 {
        margin-top: 90px; /* Ajustez cette valeur pour pousser le titre vers le bas */
        margin-bottom: 10px;
        font-size: 20px;
        color: #333;
        text-align: center;
    }

    .message {
        margin-bottom: 20px;
        padding: 15px;
        border-radius: 5px;
        font-size: 16px;
    }

    .message.success {
        background-color: #d4edda;
        color: #155724;
    }

    .message.error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        color: #333;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .form-group input:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        outline: none;
    }

    button {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background-color: #007bff;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    button:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    button:active {
        background-color: #004494;
        transform: translateY(1px);
    }

    .login-link {
        margin-top: 20px;
        text-align: center;
        font-size: 16px;
    }

    .login-link a {
        color: #007bff;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
        
    </style>
</head>
<body>
    <div class="container">
        <h1>INSCRIPTION</h1>

        <!-- Affiche les messages d'erreur ou de succès -->
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php elseif (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="creation.php" method="post">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="tel">Numéro de téléphone</label>
                <input type="tel" id="tel" name="tel" required>
            </div>
            <div class="form-group password-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group password-group">
                <label for="confirm-password">Confirmer le mot de passe</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <div class="form-group">
                <button type="submit">S'inscrire</button>
                
        </div>
        
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Téléphone</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <style>
        /* Style pour le champ de téléphone */
        .iti { width: 100%; }
    </style>
</head>
<body>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        // Initialiser le champ de téléphone avec la bibliothèque intl-tel-input
        var input = document.querySelector("#tel");
        var iti = window.intlTelInput(input, {
            preferredCountries: ["fr", "us"], // Définir les pays préférés (optionnel)
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js" // Optionnel, pour la validation et le formatage
        });
    </script>

        </form>
        <div class="login-link">
            <p>Déjà un compte? <a href="connexion.php">Se connecter</a></p>
        </div>
    </div>
</body>
</html>
