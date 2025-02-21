<?php
// user/view-notes.php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Handle note deletion
if (isset($_POST['delete_note'])) {
    $noteId = $_POST['note_id'];
    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $noteId, $userId);
    
    if ($stmt->execute()) {
        logActivity($userId, 'delete_note', "Deleted note ID: $noteId");
        $_SESSION['success'] = "Note deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete note.";
    }
}

// Fetch all notes for the user
$stmt = $conn->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$notes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <div class="header-actions">
        <h2>My Notes</h2>
        <a href="add-note.php" class="btn btn-primary">Add New Note</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($notes)): ?>
        <div class="empty-state">
            <p>No notes found. Start by adding a new note!</p>
        </div>
    <?php else: ?>
        <div class="notes-grid">
            <?php foreach ($notes as $note): ?>
                <div class="note-card">
                    <div class="note-header">
                        <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                        <span class="date">Created: <?php echo date('M d, Y', strtotime($note['created_at'])); ?></span>
                    </div>
                    
                    <div class="note-content">
                        <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                    </div>
                    
                    <div class="note-actions">
                        <a href="edit-note.php?id=<?php echo $note['id']; ?>" class="btn btn-edit">Edit</a>
                        <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this note?');">
                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                            <button type="submit" name="delete_note" class="btn btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.notes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.note-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    transition: transform 0.2s ease;
}

.note-card:hover {
    transform: translateY(-2px);
}

.note-header {
    margin-bottom: 1rem;
}

.note-header h3 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.date {
    font-size: 0.9rem;
    color: #666;
}

.note-content {
    color: #444;
    margin-bottom: 1.5rem;
    line-height: 1.6;
    max-height: 200px;
    overflow-y: auto;
}

.note-actions {
    display: flex;
    gap: 1rem;
    margin-top: auto;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background-color 0.2s;
}

.btn-primary {
    background: #4a90e2;
    color: white;
}

.btn-edit {
    background: #4a90e2;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.delete-form {
    display: inline;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    background: #f8f9fa;
    border-radius: 8px;
    color: #666;
}
</style>

<?php include '../includes/footer.php'; ?>