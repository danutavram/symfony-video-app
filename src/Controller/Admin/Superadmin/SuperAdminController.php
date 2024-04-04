<?php

namespace App\Controller\Admin\Superadmin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Utils\Interfaces\UploaderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/su/')]

class SuperAdminController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    #[Route('upload-video-locally', name: 'upload_video_locally')]
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $video->getUploadedVideo();
            $fileName = $fileUploader->upload($file);

            $base_path = Video::uploadFolder;
            $video->setPath($base_path . $fileName[0]);
            $video->setTitle($fileName[1]);

            $this->em->persist($video);
            $this->em->flush();

            return $this->redirectToRoute('videos');
        }


        return $this->render('admin/upload_video_locally.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('upload-video-by-vimeo', name: 'upload_video_by_vimeo')]
    public function uploadVideoByVimeo(Request $request): Response
    {
        $vimeo_id = preg_replace('/^\/.+\//', '', $request->get('video_uri'));
        if ($request->get('videoName') && $vimeo_id) {
            $video = new Video();
            $video->setTitle($request->get('videoName'));
            $video->setPath(Video::VimeoPath . $vimeo_id);

            $this->em->persist($video);
            $this->em->flush();

            return $this->redirectToRoute('videos');
        }


        return $this->render('admin/upload_video_vimeo.html.twig');
    }


    #[Route('delete-video/{video}/{path}', name: 'delete_video', requirements: ["path" => '.+'])]
    public function deleteVideo(Video $video, UploaderInterface $fileUploader, $path)
    {
        $this->em->remove($video);
        $this->em->flush();

        if ($fileUploader->delete($path)) {
            $this->addFlash(
                'success',
                'The video was successfully deleted.'
            );
        } else {
            $this->addFlash(
                'danger',
                'We were not able to delete. Check the video.'
            );
        }

        return $this->redirectToRoute('videos');
    }

    #[Route('update-video-category/{video}', methods: ['POST'], name: 'update_video_category')]
    public function updateVideoCategory(Request $request, Video $video)
    {
        $category = $this->em->getRepository(Category::class)->find($request->request->get('video_category'));

        $video->setCategory($category);
        $this->em->persist($video);
        $this->em->flush();

        return $this->redirectToRoute('videos');
    }


    #[Route('users', name: 'users')]
    public function users(): Response
    {
        $users = $this->em->getRepository(User::class)->findBy([], ['name' => 'ASC']);
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('delete-user/{user}', name: 'delete_user')]
    public function deleteUser(User $user): Response
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->redirectToRoute('users');
    }
}
