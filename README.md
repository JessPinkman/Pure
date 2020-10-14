# Pure

Library to create, use and reuse components to generate html.

- Combine elements freely and seamlessly.
- Easily add attributes to any element with the use of magic \_\_call.
- Create complexe structures that you can interact with by extending the class.
- Makes your code very clean and readable.

# How

There are two main classes:

## Pure\Component

    This is the class that abstracts an html markup element
    you combine them to create your html
    use magic calls to define the markup attributes

## Pure\Pure

    This is the factory, use static magic calls to create components.
    You can then chain magic calls on the component to create the component attributes
    and the method 'append' to add children

### example:

```php

use Pure\Pure;

echo Pure::h3()
    ->id('my-first-pure-component')
    ->class('badge badge-primary')
    ->my_custom_prop('pure')
    ->append('Hello World!');

```

###### output

```html
<h3
  id="my-first-pure-component"
  class="badge badge-primary"
  my-custom-prop="pure"
>
  Hello World!
</h3>
```

### Nesting:

```php

echo Pure::div()
    ->class('container')
    ->append(
        Pure::p()
            ->class('inner')
            ->append("I'm nested !")
    );

```

###### output

```html
<div class="container">
  <p class="inner">I'm nested !</p>
</div>
```

### Extend the Component class to create reusable components in your projects

```php

use Pure\Component;
use Pure\Pure;

class Select extends Component {

    function __construct( string $name )
    {
        parent::__construct( 'select' ); //create component with 'select' markup
        $this->name( $name ); //assign attribute name with passed $name in constructor
    }

    //method to abstract adding children to this component
    public function addOption( string $label, $value = null, bool $disabled = false ): self
    {

        $this->append(
            new Pure::option()
                ->value($value ?? false)
                ->disabled($disabled)
                ->append($label)
        );

        return $this;
    }

}

echo (new Select('user-preference'))
    ->class('form__input')
    ->addOption( 'Select your preference', null, true )
    ->addOption( 'A is the best', 'a' )
    ->addOption( 'B is better', 'b' )
    ->addOption( 'C is amazing', 'c' );

```

###### output

```html
<select name="user-preference" class="form__input">
  <option disabled>Select your preference</option>
  <option value="a">A is the best</option>
  <option value="b">B is better</option>
  <option value="c">C is amazing</option>
</select>
```

### More examples

```php

use Pure\Component;
use Pure\Pure;

//External Product class to illustrate example
use YourProject\Namespace\Product;

class ProductView extends Component
{

    public function __construct(array $product)
    {
        parent::__construct('article');             // create component with article markup
        $this
            ->class('product__tile')                //add class
            ->data_product_id($product['id'])       //add custom attribute
            ->append(
                Pure::h1()                          //first child, the title
                    ->class('product__tile_title')
                    ->append($product['name']),
                Pure::img()                         //second child, the image
                    ->class('product__tile_img')
                    ->src($product['imgURL'])
                    ->alt($product['name']),
                Pure::span()                        //third child, the price
                    ->class('product__tile_price tag')
                    ->class($product['promotion'] ? 'product__tile_price--promotion' : 'product__tile_price--no-promotion') //conditionally load class
                    ->append("USD $product->price"),
            );
    }
}

class GridView extends Component
{
    public function __construct(array $products)
    {
        parent::__construct('div');
        $this->id('product__grid');

        foreach ($products as $product) {
            $this->append( new ProductTileView($product));
        }
    }
}


//Example of MVC implementation, with your personal controller to redirect http requests
$products = Product::getProductList();//array of products with product information
ProjectViewController::page('product-catalogue')->render(GridView::class, $products);

```

###### output

```html
<div class="product__grid">
  <article class="product__tile" data-product-id="3">
    <h1 class="product__tile_title">Blue Sweater</h1>
    <img
      class="product__tile_img"
      src="/uploads/product-3.jpg"
      alt="Blue Sweater"
    />
    <span class="product__tile_price tag product__tile_price--promotion">
      USD 39
    </span>
  </article>
  <article class="product__tile" data-product-id="7">
    <h1 class="product__tile_title">Red Dress</h1>
    <img
      class="product__tile_img"
      src="/uploads/product-7.jpg"
      alt="Red Dress"
    />
    <span class="product__tile_price tag product__tile_price--no-promotion">
      USD 59
    </span>
  </article>
  <article class="product__tile" data-product-id="17">
    <h1 class="product__tile_title">Grey Fedora</h1>
    <img
      class="product__tile_img"
      src="/uploads/product-17.jpg"
      alt="Grey Fedora"
    />
    <span class="product__tile_price tag product__tile_price--no-promotion">
      USD 40
    </span>
  </article>
</div>
```

### MVC model

    In practice, it recommended to separate views and controllers.

```php

//Controller file

And build from here even more complexe structures...
```
