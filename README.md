# Region bundle for symfony

[![Build Status](https://github.com/siganushka/region-bundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/siganushka/region-bundle/actions/workflows/ci.yaml)
[![Latest Stable Version](https://poser.pugx.org/siganushka/region-bundle/v/stable)](https://packagist.org/packages/siganushka/region-bundle)
[![Latest Unstable Version](https://poser.pugx.org/siganushka/region-bundle/v/unstable)](https://packagist.org/packages/siganushka/region-bundle)
[![License](https://poser.pugx.org/siganushka/region-bundle/license)](https://packagist.org/packages/siganushka/region-bundle)

国内行政区划（省、市、区、乡/街道联动） [Bundle](https://symfony.com/doc/current/bundles.html)，数据来源 https://github.com/modood/Administrative-divisions-of-China

### 安装

```bash
$ composer require siganushka/region-bundle --dev-main
```

### 使用

更新数据库映射信息：

```bash
$ php bin/console doctrine:schema:update --complete --force
```

更新行政区划数据，默认仅导入省、市、区三级数据：

```bash
$ php bin/console siganushka:region:update
```

如果需要导入乡/街道四级数据，可以指定参数：

```bash
$ php bin/console siganushka:region:update --with-street
```

导入路由：

```yaml
# ./config/routes.yaml

siganushka_region:
    resource: "@SiganushkaRegionBundle/Resources/config/routes.php"
    prefix: /api
```

> 导入后可通过 `php bin/console debug:route` 查看已导入路由。

### Twig

在 `Twig` 中实现省、市、区、乡/街道四级联动，需要先导出前端资源到项目：

```bash
$ php bin/console assets:install
```

导入前端资源到页面：

> main.js 依赖 [fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)，如果你的浏览器不支持，请使用 [fetch polyfill](https://github.com/JakeChampion/fetch) 。

```html
<script src="bundles/siganushkaregion/main.js"></script>
```

### 示例

例如用户地址实体，包含省、市、区、乡/街道四级联动：

```php
// src/Entity/UserAddress.php

use Siganushka\RegionBundle\Entity\Region;

class UserAddress
{
    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $province = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $city = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $district = null;

    /**
     * @ORM\ManyToOne(targetEntity=Region::class)
     */
    private ?Region $street = null;

    // ...
}
```

> 为保证 bundle 的独立、可复用性，你的实体关联到 ``Region::class`` 时必需为单向关系，不要指定 `inversedBy` 参数。

用户地址实体表单类型：

```php
// src/Form/UserAddressType.php

use Siganushka\RegionBundle\Form\Type\RegionType;

class UserAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('province', RegionType::class, [
                'cascader_target' => 'city',
            ])
            ->add('city', RegionType::class, [
                'cascader_target' => 'district',
            ])
            ->add('district', RegionType::class, [
                'cascader_target' => 'street',
            ])
            ->add('street', RegionType::class)
        ;
    }

    // ...
}
```

> `cascader_target` 表单选项指定了此字段联动的下一级字段，不管是三级还是四级，只需要指定该参数即可，此联动功能在 `main.js` 中实现，如果你需要自己实现联动，则不需要导入 `main.js`。
