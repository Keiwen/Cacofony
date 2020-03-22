# EasyAdminBundle Extension

Extends part of [EasyAdmin bundle](https://symfony.com/doc/master/bundles/EasyAdminBundle/index.html).

## Focus page
Considering two entities with one-to-many relation,
adding links to list on 'child' entities with the same 'parent'.
In basic 'parent' list, you can display the number of 'children' related
to this 'parent'.

With focus functionnality, turn this number to a link, redirecting
to the list of 'children' related to the 'parent'. Also, the 'new' action
from this focused list will preselect the 'parent' in the form.

For exemple, if you have 'Product' and 'Category' entities, each product
refering to one category. In category list, each category will have a link
to the list of all products related to this category. 

### Setup
In route configuration, switch to the Cacofony controller: `@KeiwenCacofonyBundle/Controller/EasyAdminController.php`

routes/easy_admin.yaml
```yaml
//    resource: '@EasyAdminBundle/Controller/EasyAdminController.php'
    resource: '@KeiwenCacofonyBundle/Controller/EasyAdminController.php'
```

In package configuration, you can use the focus template: `@KeiwenCacofony/admin/field_association_focus.html.twig`

packages/easy_admin.yaml
```yaml
        Category:
            class: App\Entity\Category
            list:
                fields: ['name', {property: products, template: '@KeiwenCacofony/admin/field_association_focus.html.twig'}]
```

Focused list is using the `@KeiwenCacofony/admin/focus.html.twig` template.

### Samples
![Parent list](https://raw.githubusercontent.com/Keiwen/Cacofony/master/Resources/doc/screenshots/easyadmin_focus_parentlist.png)
![Focused list](https://raw.githubusercontent.com/Keiwen/Cacofony/master/Resources/doc/screenshots/easyadmin_focus_list.png)
![Create](https://raw.githubusercontent.com/Keiwen/Cacofony/master/Resources/doc/screenshots/easyadmin_focus_create.png)


