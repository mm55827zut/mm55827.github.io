<?php
namespace App\Model;

use App\Service\Config;

class Comment
{
    private ?int $id = null;
    private ?int $postId = null;
    private ?string $content = null;
    private ?string $gifPath = null;

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): Comment { $this->id = $id; return $this; }

    public function getPostId(): ?int { return $this->postId; }
    public function setPostId(?int $postId): Comment { $this->postId = $postId; return $this; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(?string $content): Comment { $this->content = $content; return $this; }

    public function getGifPath(): ?string { return $this->gifPath; }
    public function setGifPath(?string $gifPath): Comment { $this->gifPath = $gifPath; return $this; }

    public static function fromArray(array $data): Comment
    {
        return (new self())->fill($data);
    }

    public function fill(array $data): Comment
    {
        if (isset($data['id']) && !$this->id) {
            $this->setId((int) $data['id']);
        }
        if (isset($data['post_id'])) {
            $this->setPostId((int) $data['post_id']);
        }
        if (isset($data['content'])) {
            $this->setContent($data['content']);
        }
        if (array_key_exists('gif_path', $data)) {
            $this->setGifPath($data['gif_path']);
        }

        return $this;
    }

    private static function pdo(): \PDO
    {
        return new \PDO(
            Config::get('db_dsn'),
            Config::get('db_user'),
            Config::get('db_pass')
        );
    }

    private static function fromRows(array $rows): array
    {
        return array_map(static function (array $row): Comment {
            return self::fromArray($row);
        }, $rows);
    }

    public static function findAll(): array
    {
        $statement = self::pdo()->query('SELECT * FROM comment ORDER BY id DESC');

        return self::fromRows($statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function findByPostId(int $postId): array
    {
        $statement = self::pdo()->prepare(
            'SELECT * FROM comment WHERE post_id = :post_id ORDER BY id DESC'
        );
        $statement->execute(['post_id' => $postId]);

        return self::fromRows($statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function find(int $id): ?Comment
    {
        $statement = self::pdo()->prepare('SELECT * FROM comment WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        return $row ? self::fromArray($row) : null;
    }

    public function save(): void
    {
        $pdo = self::pdo();

        if (!$this->id) {
            $statement = $pdo->prepare(
                'INSERT INTO comment (post_id, content, gif_path)
                 VALUES (:post_id, :content, :gif_path)'
            );
            $statement->execute([
                'post_id' => $this->postId,
                'content' => $this->content,
                'gif_path' => $this->gifPath,
            ]);
            $this->setId((int) $pdo->lastInsertId());
            return;
        }

        $statement = $pdo->prepare(
            'UPDATE comment
             SET post_id = :post_id, content = :content, gif_path = :gif_path
             WHERE id = :id'
        );
        $statement->execute([
            'post_id' => $this->postId,
            'content' => $this->content,
            'gif_path' => $this->gifPath,
            'id' => $this->id,
        ]);
    }

    public function delete(): void
    {
        $statement = self::pdo()->prepare('DELETE FROM comment WHERE id = :id');
        $statement->execute(['id' => $this->id]);
    }
}
