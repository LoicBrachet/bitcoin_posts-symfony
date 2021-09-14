<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Commentaires;
use App\Form\AjoutArticleFormType;
use App\Form\CommentaireFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class ArticlesController
 * @package App\Controller
 * @Route("/actualites", name="actualites_")
 */

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="articles")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy([]);

        $articles = $paginator ->paginate(
            $donnees,
            $request->query->getInT('page', 1),
            4
        );
        return $this->render('articles/index.html.twig', [
        'article'=>$articles]);
    }
    /**
 * @Route("/{slug}", name="article")
*/
public function article($slug,  Request $request){
    // On récupère l'article correspondant au slug
    $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);
    if(!$article){
        // Si aucun article n'est trouvé, nous créons une exception
        throw $this->createNotFoundException('L\'article n\'existe pas');
    }

    $commentaire =new Commentaires();
    $form = $this->createForm(CommentaireFormType::class, $commentaire);
     // Nous récupérons les données
     $form->handleRequest($request);

     // Nous vérifions si le formulaire a été soumis et si les données sont valides
     if ($form->isSubmitted() && $form->isValid()) {
         // Hydrate notre article avec le commentaire
         $commentaire->setArticles($article);

         $doctrine = $this->getDoctrine()->getManager();

         // On hydrate notre instance $commentaire
         $doctrine->persist($commentaire);

         // On écrit en base de données
         $doctrine->flush();
     }
    // Si l'article existe nous envoyons les données à la vue
    return $this->render('articles/article.html.twig', [
        'article'=>$article,
        'formComment' =>$form->createView()
        ]);
}
 /**
     * 
     * @Route("/article/ajouter", name="ajout_article")
     */
    public function ajout(Request $request,TranslatorInterface $translator)
    {
        $article = new Articles();

        $form = $this->createForm(AjoutArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $article->setUsers($this->getUser());

		

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            $message = $translator->trans('Article published successfully');

            $this->addFlash('message', $message);
            return $this->redirectToRoute('actualites_articles');
        }


        
        return $this->render('articles/ajout.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

}
