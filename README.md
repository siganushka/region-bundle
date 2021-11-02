# Region bundle for symfony

[![Build Status](https://travis-ci.org/siganushka/region-bundle.svg?branch=main)](https://travis-ci.org/siganushka/region-bundle)
[![Latest Stable Version](https://poser.pugx.org/siganushka/region-bundle/v/stable)](https://packagist.org/packages/siganushka/region-bundle)
[![Latest Unstable Version](https://poser.pugx.org/siganushka/region-bundle/v/unstable)](https://packagist.org/packages/siganushka/region-bundle)
[![License](https://poser.pugx.org/siganushka/region-bundle/license)](https://packagist.org/packages/siganushka/region-bundle)

国内行政区划（省、市、区选择） [Bundle](https://symfony.com/doc/current/bundles.html)。

### 安装

```bash
$ composer require siganushka/region-bundle
```

### 使用

更新数据库映射信息。

```bash
$ php bin/console doctrine:schema:update --force
```

导入行政区划数据。

```bash
$ php bin/console siganushka:region:update
```

导入控制器。

```yaml
# ./config/routes.yaml

siganushka_region:
    resource: "@SiganushkaRegionBundle/Resources/config/routes.php"
```

为实体添字段，默认为省 `province`、市 `city`、区 `district` 三级。

```php
// src/Entity/Foo.php

use Siganushka\RegionBundle\Entity\RegionSubjectInterface;
use Siganushka\RegionBundle\Entity\RegionSubjectTrait;

class Foo implements RegionSubjectInterface
{
    use RegionSubjectTrait;

    // ...
}
```

添加表单字段。

```php
// src/Form/FooType.php

use Siganushka\RegionBundle\Form\Type\RegionSubjectType;

class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // region 为虚拟名称，可随便填写
            ->add('region', RegionSubjectType::class, [
                // 'province_options' => [
                //     'placeholder' => '--- 请选择 ---',
                //     'constraints' => new NotBlank(),
                // ],
                // 'city_options' => [
                //     'placeholder' => '--- 请选择 ---',
                //     'constraints' => new NotBlank(),
                // ],
                // 'district_options' => [
                //     'placeholder' => '--- 请选择 ---',
                //     'constraints' => new NotBlank(),
                // ],
            ])
        ;
    }

    // ...
}
```

客户端实现联动效果，以 `jquery` 获取为例：

```javascript
$(function() {
  var $province = $('#{{ form.region.province.vars.id }}')
  var $city = $('#{{ form.region.city.vars.id }}')
  var $district = $('#{{ form.region.district.vars.id }}')

  var update = function(parent, $target) {
    $.getJSON('{{ path("siganushka_region") }}', { parent: parent }, function(r) {
      var options = []
      $.each(r, function(idx, el) {
        options.push('<option value="'+ el.code +'">'+ el.name +'</option>')
      })
      $target.html(options.join('')).trigger('change')
    })
  }

  $province.on('change', function (event) {
    update(event.currentTarget.value, $city)
  })

  $city.on('change', function (event) {
    update(event.currentTarget.value, $district)
  })
})
```

获取数据时如果想排除某些数据，可以使用 `RegionFilterEvent` 事件过滤，比如过滤掉直辖市：

```php
// src/EventListener/RemoveDirectlyRegionListener.php

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Siganushka\RegionBundle\Event\RegionFilterEvent;
use Siganushka\RegionBundle\Entity\RegionInterface;

class RemoveDirectlyRegionListener implements EventSubscriberInterface
{
    const DIRECTLY_CODES = [110000, 120000, 310000, 500000];

    public function onRegionFilter(RegionFilterEvent $event)
    {
        $regions = array_filter($event->getRegions(), function(RegionInterface $region) {
            return !in_array($region->getCode(), self::DIRECTLY_CODES);
        });

        $event->setRegions($regions);
    }

    public static function getSubscribedEvents()
    {
        return [
            RegionFilterEvent::class => 'onRegionFilter',
        ];
    }
}
```
