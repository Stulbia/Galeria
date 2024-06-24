<?php
namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class UserVoter extends Voter
{
const VIEW = 'VIEW';
const EDIT = 'EDIT';
const DELETE = 'DELETE';

private $security;

public function __construct(Security $security)
{
$this->security = $security;
}

protected function supports(string $attribute, $subject): bool
{
return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]) && $subject instanceof User;
}

protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
{
$currentUser = $token->getUser();

if (!$currentUser instanceof User) {
// The user must be logged in; if not, deny access
return false;
}

// Admins have all permissions
if ($this->security->isGranted('ROLE_ADMIN')) {
return true;
}

/** @var User $targetUser */
$targetUser = $subject;

switch ($attribute) {
case self::VIEW:
return $this->canView($targetUser, $currentUser);
case self::EDIT:
return $this->canEdit($targetUser, $currentUser);
case self::DELETE:
return $this->canDelete($targetUser, $currentUser);
}

return false;
}

private function canView(User $targetUser, User $currentUser): bool
{
// Regular users can view their own profile
return $currentUser === $targetUser;
}

private function canEdit(User $targetUser, User $currentUser): bool
{
// Regular users can edit their own profile
return $currentUser === $targetUser;
}

private function canDelete(User $targetUser, User $currentUser): bool
{
// Regular users cannot delete any user
return false;
}
}
