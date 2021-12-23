<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\AnnonceSonorise;
use App\Repository\OrganisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    private $parameterBag;
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;

    public function __construct(ParameterBagInterface $parameterBag, OrganisationRepository $organisationRepository)
    {
        $this->parameterBag = $parameterBag;

        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @Route("/import", name="import")
     */
    public function index(): Response
    {
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }

    /**
     * @Route("/importPost", name="importPost")
     */
    public function testphp(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')) {

            $target = $this->parameterBag->get('kernel.project_dir') . "/public";
            $file = $request->files->get('file');
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $nomFichier = $originalFilename . '.' . $file->guessExtension();


            try {
                $file->move(
                    $target,
                    $nomFichier
                );
            } catch (FileException $e) {
                dump($e->getMessage());
                die;
                die('erreur lors du deplacement du fichier');
            }

            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($nomFichier);

            /**  Create a new Reader of the type that has been identified  **/
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

            /**  Load $inputFileName to a Spreadsheet Object  **/
            $spreadsheet = $reader->load($nomFichier);

            /**  Convert Spreadsheet Object to an Array for ease of use  **/
            $excel_datas = $spreadsheet->getActiveSheet()->toArray();
            unset($excel_datas[0]);
            foreach ($excel_datas as $excel_data) {

                list($reference, $secteurActivite, $organisation, $titreAnnonce,
                    $description, $profileRecherche, $datePublication, $dureeInterim,
                    $dateExpiration, $niveauEtude, $experiencePro, $typeContrat,
                    $salaire, $remuneration, $lieu, $publiePar, $url_sonorisation) = $excel_data;


                // SAVE DATAS
                $uniqueId = md5(uniqid());
                $uniqueId = substr($uniqueId, 0, 7);
                $rand = substr($uniqueId, 0, 2);

                if (!$reference) {
                    $reference = "ANN-SON-" . $rand . "-" . strtoupper($uniqueId);
                }
                if ($reference !== "" && $reference !== null) {
                    $organisationObj = $this->organisationRepository->findOneBy(['num_entreprise' => $organisation]);
                    if ($organisationObj) {
                        $annonce = (new Annonce())
                            ->setReference($reference)
                            ->setOrganisation($organisationObj)
                            ->setTitre($titreAnnonce ? $titreAnnonce : " ")
                            ->setDescription($description ? $description : " ")
                            ->setProfilRecherche($profileRecherche ? $profileRecherche : " ")
                            ->setDateExpiration($this->formatDateIncoming($dateExpiration))
                            ->setDureeInterim($dureeInterim ? $dureeInterim : " ")
                            ->setNiveauEtude($niveauEtude ? $niveauEtude : " ")
                            ->setExperienceProfessionnel($experiencePro ? $experiencePro : " ")
                            ->setTypeContrat($typeContrat ? $typeContrat : " ")
                            ->setSalaire($salaire ? $salaire : " ")
                            ->setUser($this->getUser())
                            ->setRemuneration($remuneration ? $remuneration : " ")
                            ->setLieu($lieu ? $lieu : " ");


                        if ($url_sonorisation && $url_sonorisation !== "" && $url_sonorisation !== " ") {
                            if ($organisationObj->getSonorisationAutomatique()) {
                                $annonceSonorise = (new AnnonceSonorise())
                                    ->setAnnonce($annonce)
                                    ->setUrlSono($url_sonorisation)
                                    ->setReference($reference)
                                    ->setTypeBouton("audio")
                                    ->setAnnonceUrl("")
                                    ->setStatutValidation("")
                                    ->setUser($this->getUser());

                                $annonce->setEstSonorise(true);
                                $em->persist($annonceSonorise);
                            }

                        }
                        $em->persist($annonce);
                    }
                }
            }
            $em->flush();
        }
        return new JsonResponse(['success' => true]);


        return $this->render('test.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    function formatDateIncoming($string)
    {


        try {
            $dateArray = explode("/", $string);
            $dateString = $dateArray[1] . '-' . $dateArray[0] . '-' . $dateArray[2];
            if ($dateArray[1] < 13) {
                $dateString = $dateArray[0] . '-' . $dateArray[1] . '-' . $dateArray[2];
            } else {
                $dateString = $dateArray[1] . '-' . $dateArray[0] . '-' . $dateArray[2];
            };
            return new \DateTime($dateString);
        } catch (\Exception $e) {
            return new \DateTime();
        }


    }

    function clean($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
