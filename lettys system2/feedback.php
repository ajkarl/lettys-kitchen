<?php
// Include the database connection
include('db.php');

// Fetch feedback from the database
try {
    $feedbackQuery = $pdo->query("SELECT customer_name, feedback, feedback_date FROM feedback ORDER BY feedback_date DESC");
    $feedbacks = $feedbackQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching feedback: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <?php include('header.php'); ?>

    <!-- Feedback Content -->
    <main class="container mx-auto py-8 px-6">
        <h1 class="text-2xl font-bold mb-6">Customer Feedback</h1>

        <?php if (!empty($feedbacks)): ?>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="table-auto w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-200 text-left">
                            <th class="p-4 border-b border-gray-300">Customer Name</th>
                            <th class="p-4 border-b border-gray-300">Feedback</th>
                            <th class="p-4 border-b border-gray-300">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td class="p-4 border-b border-gray-200"><?= htmlspecialchars($feedback['customer_name']) ?></td>
                                <td class="p-4 border-b border-gray-200"><?= htmlspecialchars($feedback['feedback']) ?></td>
                                <td class="p-4 border-b border-gray-200"><?= htmlspecialchars($feedback['feedback_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-700">No feedback available at the moment.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>
</html>
