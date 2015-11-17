# Add to documentation #

### Add MediaBundle to assetic bundles to your app/config.yml ###

    assetic:
      [...]
      bundles:
        [...]
        - MykeesMediaBundle

### Add MediaBundle widget twig template to twig app/config.yml ###

    twig:
      [...]
    form_themes:
      - MykeesMediaBundle:Media:fields.html.twig

### Example usage of widget ###
In controller, pass in the Media service

    // YourBundle/Controller/YourFormController.php
    [...]
    private function createCreateForm(EntityName $entity)
    {
        $form = $this->createForm(new EntityNameType($this->get('mk.media.manager')), $entity, array(
            'action' => $this->generateUrl('blahblah'),
            'method' => 'POST',
        ));
          
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }

Then in your FormType

    // YourBundle/Form/YourFormType.php
    [...]
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            [...]
            ->add('yourImageField', 'singleimage', array(
                'required' => false,
                'entities' => $this->mk->findMediasByModel($options['this_model_name'])
            ))
        ;
    }
