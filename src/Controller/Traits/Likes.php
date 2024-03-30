<?php 

namespace App\Controller\Traits;

use App\Entity\User;

trait Likes {
    private function likeVideo($video)
    {  
        $user = $this->em->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);

        $this->em->persist($user);
        $this->em->flush(); 
        return 'liked';
    }
    private function dislikeVideo($video)
    {
        $user = $this->em->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);

        $this->em->persist($user);
        $this->em->flush(); 
        return 'disliked';
    }
    private function undoLikeVideo($video)
    {  
        $user = $this->em->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);

        $this->em->persist($user);
        $this->em->flush();  
        return 'undo liked';
    }
    private function undoDislikeVideo($video)
    {   
        $user = $this->em->getRepository(User::class)->find($this->getUser());
        $user->removeDislikedVideo($video);

        $this->em->persist($user);
        $this->em->flush(); 
        return 'undo disliked';
    }
}