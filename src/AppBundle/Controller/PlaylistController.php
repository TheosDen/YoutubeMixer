<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Playlist controller.
 *
 * @Route("playlist")
 */
class PlaylistController extends Controller
{
    /**
     * Lists all playlist entities.
     *
     * @Route("/", name="playlist_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $playlists = $em->getRepository('AppBundle:Playlist')->findAll();

//        VarDumper::dump($playlists);
//        exit;

        return $this->render('playlist/index.html.twig', array(
            'playlists' => $playlists,
        ));
    }

    /**
     * Creates a new playlist entity.
     *
     * @Route("/new", name="playlist_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $playlist = new Playlist();
        $form = $this->createForm('AppBundle\Form\PlaylistType', $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($playlist);
            $em->flush();

            return $this->redirectToRoute('playlist_show', array('id' => $playlist->getId()));
        }

        return $this->render('playlist/new.html.twig', array(
            'playlist' => $playlist,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a playlist entity.
     *
     * @Route("/{id}", name="playlist_show")
     * @Method("GET")
     */
    public function showAction(Playlist $playlist)
    {
        $deleteForm = $this->createDeleteForm($playlist);

        return $this->render('playlist/show.html.twig', array(
            'playlist' => $playlist,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing playlist entity.
     *
     * @Route("/{id}/edit", name="playlist_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Playlist $playlist)
    {
        $deleteForm = $this->createDeleteForm($playlist);
        $editForm = $this->createForm('AppBundle\Form\PlaylistType', $playlist);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('playlist_edit', array('id' => $playlist->getId()));
        }

        return $this->render('playlist/edit.html.twig', array(
            'playlist' => $playlist,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a playlist entity.
     *
     * @Route("/{id}", name="playlist_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Playlist $playlist)
    {
        $form = $this->createDeleteForm($playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($playlist);
            $em->flush();
        }

        return $this->redirectToRoute('playlist_index');
    }

    /**
     * Creates a form to delete a playlist entity.
     *
     * @param Playlist $playlist The playlist entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Playlist $playlist)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('playlist_delete', array('id' => $playlist->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
