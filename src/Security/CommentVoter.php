<?php

use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter {

    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT])) {
            return false;
        }

        // only vote on `comment` objects
        if (!$subject instanceof Comment) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Comment object, thanks to `supports()`
        /** @var Post $post */
        $comment = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($comment, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Comment $comment, User $user): bool
    {
        // this assumes that the Post object has a `getOwner()` method
        return $user === $comment->getUser();
    }

}