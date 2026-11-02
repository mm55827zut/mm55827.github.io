<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Comment;
use App\Model\Post;
use App\Service\Router;
use App\Service\Templating;

class CommentController
{
    private const MAX_GIF_SIZE = 5242880;

    public function indexAction(Templating $templating, Router $router): ?string
    {
        return $templating->render('comment/index.html.php', [
            'comments' => Comment::findAll(),
            'router' => $router,
        ]);
    }

    public function createAction(
        ?array $requestPost,
        ?array $requestFiles,
        int $postId,
        Templating $templating,
        Router $router
    ): ?string {
        $comment = new Comment();

        if ($requestPost) {
            $comment->fill($requestPost);

            if (!Post::find($comment->getPostId() ?? 0)) {
                throw new NotFoundException('Missing post for this comment');
            }

            $comment->setGifPath($this->uploadedGif($requestFiles['gif'] ?? null));
            $comment->save();

            $router->redirect($router->generatePath('comment-index'));
            return null;
        }

        if ($postId) {
            if (!Post::find($postId)) {
                throw new NotFoundException("Missing post with id $postId");
            }
            $comment->setPostId($postId);
        }

        return $templating->render('comment/create.html.php', [
            'comment' => $comment,
            'posts' => Post::findAll(),
            'router' => $router,
        ]);
    }

    public function editAction(
        int $commentId,
        ?array $requestPost,
        ?array $requestFiles,
        Templating $templating,
        Router $router
    ): ?string {
        $comment = Comment::find($commentId);
        if (!$comment) {
            throw new NotFoundException("Missing comment with id $commentId");
        }

        if ($requestPost) {
            $oldGifPath = $comment->getGifPath();
            $comment->fill($requestPost);

            if (!Post::find($comment->getPostId() ?? 0)) {
                throw new NotFoundException('Missing post for this comment');
            }

            $newGifPath = $this->uploadedGif($requestFiles['gif'] ?? null);
            if ($newGifPath !== null) {
                $comment->setGifPath($newGifPath);
            }

            $comment->save();

            if ($newGifPath !== null) {
                $this->removeGif($oldGifPath);
            }

            $router->redirect($router->generatePath('comment-index'));
            return null;
        }

        return $templating->render('comment/edit.html.php', [
            'comment' => $comment,
            'posts' => Post::findAll(),
            'router' => $router,
        ]);
    }

    public function showAction(
        int $commentId,
        Templating $templating,
        Router $router
    ): ?string {
        $comment = Comment::find($commentId);
        if (!$comment) {
            throw new NotFoundException("Missing comment with id $commentId");
        }

        return $templating->render('comment/show.html.php', [
            'comment' => $comment,
            'router' => $router,
        ]);
    }

    public function deleteAction(int $commentId, Router $router): ?string
    {
        $comment = Comment::find($commentId);
        if (!$comment) {
            throw new NotFoundException("Missing comment with id $commentId");
        }

        $gifPath = $comment->getGifPath();
        $comment->delete();
        $this->removeGif($gifPath);

        $router->redirect($router->generatePath('comment-index'));
        return null;
    }

    private function uploadedGif(?array $file): ?string
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('GIF upload failed.');
        }
        if (($file['size'] ?? 0) > self::MAX_GIF_SIZE) {
            throw new \RuntimeException('GIF must be 5 MB or smaller.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        if ($finfo->file($file['tmp_name']) !== 'image/gif') {
            throw new \RuntimeException('Only GIF files are allowed.');
        }

        $directory = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR
            . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'comments';

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException('Could not create the GIF upload directory.');
        }

        $filename = bin2hex(random_bytes(16)) . '.gif';
        if (!move_uploaded_file($file['tmp_name'], $directory . DIRECTORY_SEPARATOR . $filename)) {
            throw new \RuntimeException('Could not save the uploaded GIF.');
        }

        return '/uploads/comments/' . $filename;
    }

    private function removeGif(?string $path): void
    {
        if (!$path || !preg_match('~^/uploads/comments/[a-f0-9]{32}\.gif$~', $path)) {
            return;
        }

        $file = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public'
            . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'));

        if (is_file($file)) {
            unlink($file);
        }
    }
}