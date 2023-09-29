<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;

class UniversityController extends FrontendController
{
    public function universityAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('university/home.html.twig');
    }
    public function courseAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('university/course.html.twig');
    }
    public function departmentAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('university/department.html.twig');
    }
    public function facultyAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('university/faculty.html.twig');
    }
    public function studentAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('university/student.html.twig');
    }
    public function snippetAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {

        return $this->render('footer.html.twig');
    }

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

}
