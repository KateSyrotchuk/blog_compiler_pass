<?php
/**
 * Created by PhpStorm.
 * User: kate
 * Date: 19.08.16
 * Time: 10:20
 */

namespace AppBundle\Form;


use AppBundle\Entity\Category;
use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Repository\CategoryRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TypesOfPost
{
    private $forms;

    public function __construct()
    {
        $this->forms = array();
    }

    public function addForm(AbstractType $form, $alias)
    {
        $this->forms[$alias] = $form;
    }

    public function getForm($alias)
    {
        if (array_key_exists($alias, $this->forms)) {
            return $this->forms[$alias];
        }
    }

    public function getForms()
    {
        return $this->forms;
    }

}