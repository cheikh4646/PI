<?php
session_start();
require_once 'includes/db.php';

// Vérification d'accès
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Récupérer les emplois du temps selon le rôle
try {
    if ($role === 'admin') {
        $stmt = $pdo->query("SELECT * FROM timetables");
    } elseif ($role === 'professor') {
        $stmt = $pdo->prepare("SELECT * FROM timetables WHERE professor_id = ?");
        $stmt->execute([$user_id]);
    } elseif ($role === 'student') {
        $stmt = $pdo->prepare("SELECT * FROM timetables WHERE group_id = ? AND year_id = ?");
        $stmt->execute([$_SESSION['group_id'], $_SESSION['year_id']]);
    } else {
        echo "Rôle non reconnu.";
        exit;
    }

    $timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Emploi du Temps</title>
</head>
<body>
    <h1>Emploi du Temps - <?php echo ucfirst($role); ?></h1>

    <?php if ($role === 'admin'): ?>
    <h2>Ajouter un cours</h2>
    <form method="post" action="add_timetable.php">
        <p><input type="text" name="title" placeholder="Nom du cours" required></p>
        <p>
            <select name="day_of_week" required>
                <option value="">Jour</option>
                <option value="Lundi">Lundi</option>
                <option value="Mardi">Mardi</option>
                <option value="Mercredi">Mercredi</option>
                <option value="Jeudi">Jeudi</option>
                <option value="Vendredi">Vendredi</option>
            </select>
        </p>
        <p><input type="time" name="start_time" required> à <input type="time" name="end_time" required></p>
        <p><input type="number" name="group_id" placeholder="ID Groupe" required></p>
        <p><input type="number" name="year_id" placeholder="ID Année" required></p>
        <p><input type="number" name="professor_id" placeholder="ID Professeur" required></p>
        <button type="submit">Ajouter</button>
    </form>
    <hr>
    <?php endif; ?>

    <h2>Liste des cours</h2>
    <?php if ($timetables): ?>
        <table border="1">
            <tr>
                <th>Cours</th>
                <th>Jour</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Groupe</th>
                <th>Année</th>
                <th>Professeur</th>
            </tr>
            <?php foreach ($timetables as $row) ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['day_of_week']) ?></td>
                <td><?= htmlspecialchars($row['start_time']) ?></td>
                <td><?= htmlspecialchars($row['end_time']) ?></td>
                <td><?= htmlspecialchars($row['group_id']) ?></td>
                <td><?= htmlspecialchars($row['year_id']) ?></td>
                <td><?= htmlspecialchars($row['professor_id']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucun cours trouvé.</p>
    <?php endif; ?>

    <p><a href="logout.php">Déconnexion</a></p>
</body>
</html>
