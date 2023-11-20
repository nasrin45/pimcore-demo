<?php

namespace App\Controller;

use Exception;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;
use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Exception\DefinitionWriteException;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\Course;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Pimcore\Model\DataObject\Department;
use Pimcore\Model\DataObject\Faculty;
use Pimcore\Model\DataObject\Home;
use Pimcore\Model\DataObject\Student;
use Pimcore\Model\Document\Link;
use Pimcore\Model\Element\Note;
use Pimcore\Model\User;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniversityController extends FrontendController
{
    /**
     * @throws DefinitionWriteException
     * @throws Exception
     */
    public function universityAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

//        $tag =  new \Pimcore\Model\Element\Tag();
//        try {
//            $tag->setName('Type')->save();
//            \Pimcore\Model\Element\Tag::addTagToElement('object', 12, $tag);
//        } catch (Exception $e) {
//            // ....
//        }


        $about = Home::getById(12);
        $aboutText = $about->getAbout('fr');

        $home = new \App\Model\DataObject\Home();
        $home->setCampus('MCC');

        $note = new Note();
        $note->setElement($about);
        $note->setDate(time());
        $note->setType("notice");
        $note->setTitle("This is modified");
        $note->setUser(0);
        $note->addData("myModifiedText", "modifiedText", "Some Modified Text");
        $note->save();

        $class = ClassDefinition::getById(5);
        $fields = $class->getFieldDefinitions();

        foreach ($fields as $field) {
            $field->setLocked(true);
        }

        $class->save();

        $dataObject = Home::getById(12);
        $tableData = $dataObject->getAnnouncement();

        return $this->render('university/home.html.twig',[
            'aboutText'=>$aboutText,
            'home' => $home,
            'tableData'=>$tableData,
        ]);
    }


    /**
     * @throws Exception
     */
    public function courseAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $course = Course::getById(19);

//        $versions = $course->getVersions();


        $courseBrick = new \Pimcore\Model\DataObject\Objectbrick\Data\Course($course);
        $courseBrick->setName("CS");
        $courseBrick->setSubjects(["Subject1", "Subject2"]);
        $courseBrick->setDuration(3.0);

        $course->getCourse()->setCourse($courseBrick);

        $course->save();

        return $this->render('university/course.html.twig', [
//            'version'=> $versions,
            'course' => $course,
        ]);
    }
    /**
     * @Route("/department/{departmentId}", name="dept_info")
     */
    public function departmentAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        $d = Link::getById(16);
        echo($d->getHref());


        $classificationStoreData = [];

        $dataObject = Department::getById(10);
        $faculty = $dataObject->getFaculty();
        $classificationStore = $dataObject->getDept();

        foreach ($classificationStore->getGroups() as $group) {
            $groupData = [
                'groupName' => $group->getConfiguration()->getName(),
                'keys' => []
            ];

            foreach ($group->getKeys() as $key) {
                $keyConfiguration = $key->getConfiguration();

                $value = $key->getValue();
                if ($value instanceof \Pimcore\Model\DataObject\Data\QuantityValue) {
                    $value = (string) $value;
                }

                $groupData['keys'][] = [
                    'id' => $keyConfiguration->getId(),
                    'name' => $keyConfiguration->getName(),
                    'value' => $value,
                    'isQuantityValue' => ($key->getFieldDefinition() instanceof QuantityValue),
                ];
            }

            $classificationStoreData[] = $groupData;
        }

//        $keyConfig = new \Pimcore\Model\DataObject\Classificationstore\KeyConfig();
//        $keyConfig->setName("Name");
//        $keyConfig->setDescription("");
//        $keyConfig->setEnabled(true);
//        $keyConfig->setType("text");
//        $keyConfig->save();

        $object = Department::getById(10);
        $blockItems = $object->getGeographic();
        $firstBlockItem = $blockItems[0];
        $geoCoordinates = $firstBlockItem["coordinates"]->getData();
        $geoBounds = $firstBlockItem["bounds"]->getData();

        if ($geoCoordinates instanceof \Pimcore\Model\DataObject\Data\GeoCoordinates) {
            $latitude = $geoCoordinates->getLatitude();
            $longitude = $geoCoordinates->getLongitude();
        }else {
            $latitude = null;
            $longitude = null;
        }


        return $this->render('university/department.html.twig', [
            'faculty' => $faculty,
            'classificationStoreData' => $classificationStoreData,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'geoBounds' => $geoBounds,
        ]);

    }
    public function facultyAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $faculty = Faculty::getById(8);
//
//        $user =
//            User::create([
//            "parentId" => 1,
//            "username" => "faculty",
//            "password" => "password",
//            "hasCredentials" => true,
//            "active" => true
//        ]);
//
//        $faculties = new Faculty();
//        $faculties->setUser($user->getId());

        $campus = new \App\Model\DataObject\Home();
        $campus->setCampus('MCC');


        return $this->render('university/faculty.html.twig', [
            'campus' => $campus,
            'faculty' => $faculty,
        ]);
    }
    public function studentAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $dataObject = Student::getById(11);
        $imageGalleryData = $dataObject->getUniversity();
        $structuredTable = $dataObject->getResults();
        if ($structuredTable instanceof \Pimcore\Model\DataObject\Data\StructuredTable) {
            $rows = $structuredTable->getData();

            return $this->render('university/student.html.twig', [
                'imageGalleryData' => $imageGalleryData,
                'structuredTableData' => $rows,
            ]);
        }
        return $this->render('university/student.html.twig');
    }
    public function snippetAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('footer.html.twig');
    }

    /**
     * @Route("/notify", name="notify")
     */
    public function notifyAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('notify.html.twig');
    }
    public function testAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('test.html.twig');
    }

    #[Template('university/gallery.html.twig')]
    public function galleryAction(Request $request): array
    {
        if ('asset' === $request->get('type')) {
            $asset = Asset::getById((int) $request->get('id'));
            if ('folder' === $asset->getType()) {
                return [
                    'assets' => $asset->getChildren()
                ];
            }
        }

        return [];
    }

    public function newAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $custom = Course::getById(19);

        return $this->render('new.html.twig', [
            'custom' => $custom,
        ]);
    }

    /**
     * @Route("/iframe/summary")
     */
    public function summaryAction(Request $request): Response
    {
        $context = json_decode($request->get("context"), true);
        $objectId = $context["objectId"];

        $language = $context["language"] ?? "default_language";

        $object = Service::getElementFromSession('object', $objectId, '');

        if ($object === null) {
            $object = Service::getElementById('object', $objectId);
        }

        $response =  '<h1>Title for language "' . $language . '": '  . $object->getData($language) . "</h1>";

        $response .= '<h2>Context</h2>';
        $response .= array_to_html_attribute_string($context);
        return new Response($response);
    }

    public function loggerAction(ApplicationLogger $logger): void
    {
        $logger->error('Your error message');
        $logger->alert('Your alert');
        $logger->debug('Your debug message', ['foo' => 'bar']);
    }

}
