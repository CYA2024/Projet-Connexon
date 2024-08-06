<?php
session_start();

// Connexion à la base de données
try {
    $db = new PDO("mysql:host=localhost;dbname=lep-fbf", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());
    echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    die();
}

if (isset($_GET['id'])) {
    $id_joueur = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id_joueur) {
        try {
            // Récupération des données du joueur
            $stmt = $db->prepare("SELECT j.id_joueur, j.nom, j.prenom, j.datnaissance, j.ville, i.categorie, i.club, i.annee
                                  FROM joueur j
                                  JOIN inscription i ON j.id_joueur = i.id_joueur
                                  WHERE j.id_joueur = :id_joueur");
            $stmt->bindParam(':id_joueur', $id_joueur, PDO::PARAM_INT);
            $stmt->execute();
            $joueur = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$joueur) {
                echo "Joueur non trouvé.";
                exit();
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des données : " . $e->getMessage());
            echo "Une erreur est survenue. Veuillez réessayer plus tard.";
            exit();
        }
    } else {
        echo "ID du joueur invalide.";
        exit();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_joueur = filter_var($_POST['id_joueur'], FILTER_SANITIZE_NUMBER_INT);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $datnaissance = htmlspecialchars($_POST['datnaissance']);
    $ville = htmlspecialchars($_POST['ville']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $club = htmlspecialchars($_POST['club']);
    $annee = htmlspecialchars($_POST['annee']);

    try {
        // Mise à jour des informations du joueur
        $stmt = $db->prepare("UPDATE joueur SET nom = :nom, prenom = :prenom, datnaissance = :datnaissance, ville = :ville WHERE id_joueur = :id_joueur");
        $stmt->bindParam(':id_joueur', $id_joueur, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':datnaissance', $datnaissance);
        $stmt->bindParam(':ville', $ville);
        $stmt->execute();

        // Mise à jour de l'inscription
        $stmt = $db->prepare("UPDATE inscription SET categorie = :categorie, club = :club, annee = :annee WHERE id_joueur = :id_joueur");
        $stmt->bindParam(':categorie', $categorie);
        $stmt->bindParam(':club', $club);
        $stmt->bindParam(':annee', $annee);
        $stmt->bindParam(':id_joueur', $id_joueur, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['message'] = "Informations mises à jour avec succès !";
        header("Location: joueur.php");
        exit();

    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour : " . $e->getMessage());
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le joueur</title>
    <style>
        .container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn2 {
            background-color: green;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-cancel {
            background-color: gray;
            color: white;
            border: none;
            padding: 6px 12px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
    </style>
</head>
<body>

<h1>Modifier les informations du joueur</h1>

<form action="modifier.php" method="post">
    <fieldset>
        <legend><b>Modifier les informations du joueur</b></legend>
        <div class="container">
            <table>
                <tr>
                    <td><label for="nom">Nom:</label></td>
                    <td><input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($joueur['nom']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="prenom">Prénom:</label></td>
                    <td><input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($joueur['prenom']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="datnaissance">Date de naissance:</label></td>
                    <td><input type="text" name="datnaissance" id="datnaissance" value="<?php echo htmlspecialchars($joueur['datnaissance']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="ville">Ville:</label></td>
                    <td><input type="text" name="ville" id="ville" value="<?php echo htmlspecialchars($joueur['ville']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="categorie">Catégorie:</label></td>
                    <td>
                        <select id="categorie" name="categorie" required>
                            <option value="junior" <?php echo $joueur['categorie'] == 'junior' ? 'selected' : ''; ?>>junior</option>
                            <option value="senior" <?php echo $joueur['categorie'] == 'senior' ? 'selected' : ''; ?>>senior</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="club">Club:</label></td>
                    <td>
                        <select id="club" name="club" required>
                            <option value="ASPAC FC" <?php echo $joueur['club'] == 'ASPAC FC' ? 'selected' : ''; ?>>ASPAC FC</option>
                            <option value="MOGAS FC" <?php echo $joueur['club'] == 'MOGAS FC' ? 'selected' : ''; ?>>MOGAS FC</option>
                            <option value="REQUINS FC" <?php echo $joueur['club'] == 'REQUINS FC' ? 'selected' : ''; ?>>REQUINS FC</option>
                            <option value="DRAGON FC" <?php echo $joueur['club'] == 'DRAGON FC' ? 'selected' : ''; ?>>DRAGON FC</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="annee">Année:</label></td>
                    <td><input type="text" name="annee" id="annee" value="<?php echo htmlspecialchars($joueur['annee']); ?>" required></td>
                </tr>
                <tr>
                    <td><input type="hidden" name="id_joueur" value="<?php echo htmlspecialchars($joueur['id_joueur']); ?>"></td>
                    <td>
                        <input type="submit" value="Enregistrer" class="btn2">
                        <a href="joueur.php" class="btn-cancel">Annuler</a>
                    </td>
                </tr>
            </table>
        </div>
    </fieldset>
</form>

</body>
</html>
