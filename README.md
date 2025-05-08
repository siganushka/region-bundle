# Region bundle for symfony

[![Build Status](https://github.com/siganushka/region-bundle/actions/workflows/ci.yaml/badge.svg)](https://github.com/siganushka/region-bundle/actions/workflows/ci.yaml)
[![Latest Stable Version](https://poser.pugx.org/siganushka/region-bundle/v/stable)](https://packagist.org/packages/siganushka/region-bundle)
[![Latest Unstable Version](https://poser.pugx.org/siganushka/region-bundle/v/unstable)](https://packagist.org/packages/siganushka/region-bundle)
[![License](https://poser.pugx.org/siganushka/region-bundle/license)](https://packagist.org/packages/siganushka/region-bundle)

国内行政区划四级联动 [Bundle](https://symfony.com/doc/current/bundles.html)，数据来源 [Administrative-divisions-of-China](https://github.com/modood/Administrative-divisions-of-China)

### 说明

- 一级：省、自治区、直辖市
- 二级：市、县、自治州、自治县
- 三级：区、县
- 四级：乡镇/街道

### 安装

```bash
$ composer require siganushka/region-bundle
```

### 使用

更新数据库映射信息：

```bash
$ php bin/console doctrine:schema:update --force
```

导入行政区划数据：

```bash
$ php bin/console siganushka:region:update
```

> 默认仅导入三级，使用 `--with-street` 参数可导入四级数据。

导入路由：

```yaml
# ./config/routes.yaml

siganushka_region:
    resource: "@SiganushkaRegionBundle/config/routes.php"
    prefix: /api
```

> 导入后可通过 `php bin/console debug:route` 查看已导入路由。

### 示例

实体对象：

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

> 为保证独立和可复用性，关联到 `Region::class` 时必需为单向关系，不要指定 `inversedBy` 参数。

表单类型：

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

> 选项 `cascader_target` 指定了要联动的下一级字段，不管是二级、三级还是四级，只需要指定该参数即可。前端联动效果由 [AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html) 和 [StimulusBundle](https://symfony.com/bundles/StimulusBundle/current/index.html) 实现。
