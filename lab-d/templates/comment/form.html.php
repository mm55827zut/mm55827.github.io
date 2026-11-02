<?php
$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>
<div class="form-group">
    <label for="post_id">Post</label>
    <select id="post_id" name="comment[post_id]" required>
        <?php foreach ($posts as $post): ?>
            <option value="<?= $post->getId() ?>"
                <?= $comment->getPostId() === $post->getId() ? 'selected' : '' ?>>
                <?= $escape($post->getSubject()) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
    <label for="content">Comment</label>
    <textarea id="content" name="comment[content]" required><?= $escape($comment->getContent()) ?></textarea>
</div>

<div class="form-group">
    <label for="gif">GIF (optional, max 5 MB)</label>
    <input type="file" id="gif" name="gif" accept="image/gif">
</div>

<div class="form-group">
    <input type="submit" value="Submit">
</div>