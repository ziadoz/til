<?php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
 
class CreateForm extends AbstractType
{
    /**
     * Build form.
     * @param  FormBuilderInterface $builder
     * @param  array                $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        // Aide Details.
        $builder->add('status', 'choice', array(
            'choices' => array(
                'Active'    => 'Active', 
                'Archived'  => 'Archived',
            ),
            'data'              => 'Active',
            'required'          => true,
            'invalid_message'   => 'Please choose a valid status.',
            'constraints'       => new Assert\NotBlank(array('message' => 'Please choose a status.')),
            'attr'              => array('styled' => true),
        ));

        $builder->add('first', 'text', array(
            'label'         => 'First Name',
            'required'      => true,
            'constraints'   => new Assert\NotBlank(array('message' => 'Please enter a first name.')),
        ));

        $builder->add('last', 'text', array(
            'label'         => 'Last Name',
            'required'      => true,
            'constraints'   => new Assert\NotBlank(array('message' => 'Please enter a last name.')),
        ));

        // Address Details.
        $builder->add('address', 'text', array(
            'label'         => 'Address',
            'required'      => false,
        ));

        $builder->add('city', 'text', array(
            'label'         => 'City',
            'required'      => false,
        ));

        $builder->add('province', 'choice', array(
            'choices'           => array(),
            'required'          => false,
            'invalid_message'   => 'Please choose a valid province.',
        ));     

        $builder->add('postcode', 'text', array(
            'label'         => 'Postal Code',
            'required'      => false,
        ));

        $builder->add('country', 'choice', array(
            'choices'           => array('CA' => 'Canada'),
            'required'          => false,
            'empty_value'       => false,
            'data'              => 'CA',
            'invalid_message'   => 'Please choose a valid country.',
            'constraints'       => new Assert\Country(array('message' => 'Please choose a valid country.')),
        ));

        // Contact Details.
        $builder->add('phone', 'text', array(
            'label'         => 'Phone',
            'required'      => true,
            'constraints'   => array(
                new Assert\NotBlank(array('message' => 'Please enter a phone number.')),
            ),
        ));

        $builder->add('cell', 'text', array(
            'label'         => 'Cell',
            'required'      => false,
            'constraints'   => array(),
        ));     

        $builder->add('email', 'email', array(
            'label'         => 'Email',
            'required'      => false,
            'constraints'   => new Assert\Email(array('message' => 'Please enter a valid email address.')),
        ));

        // Login Details.
        $builder->add('can_login', 'checkbox', array(
            'label'     => 'Can Login?',
            'required'  => false,
        ));

        $builder->add('login_email', 'email', array(
            'label'         => 'Email',
            'required'      => false,
        ));

        $builder->add('login_password', 'repeated', array(
            'type'              => 'password',
            'invalid_message'   => 'Please enter and confirm your new password.',
            'required'          => false,
            'first_options'     => array('label' => 'Password'),
            'second_options'    => array('label' => 'Confirm Password'),
        ));

        // Events.
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Add constraints to login email and password fields.
            if (isset($data['can_login']) && $data['can_login']) {
                $email      = $form->get('login_email');
                $options    = $email->getConfig()->getOptions();
                $type       = $email->getConfig()->getType()->getName();

                $options['required']    = true;
                $options['constraints'] = new Assert\Email(array('message' => 'Please enter a valid email address.'));

                $form->add('login_email', $type, $options);

                $password   = $form->get('login_password');
                $options    = $password->getConfig()->getOptions();
                $type       = $password->getConfig()->getType()->getName();

                $options['required']    = true;
                $options['constraints'] = array(
                    new Assert\NotBlank(array(
                        'message' => 'Please enter a password.',
                    )),
                    new Assert\Length(array(
                        'min'           => 6,
                        'minMessage'    => 'Password must be more than {{ limit }} characters long.',
                    )),
                );

                $form->add('login_password', $type, $options);
            }
        });
    }

    /**
     * Form name.
     *
     * @return string
     **/
    public function getName()
    {
        return 'user';
    }
}