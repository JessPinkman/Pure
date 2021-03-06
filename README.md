# Pure

Library to create, use and reuse components to generate html.

- Combine elements freely and seamlessly.
- Easily add attributes to any element with the use of magic \_\_call.
- Create complexe structures that you can interact with by extending the class.
- Makes your code very clean and readable.

**Also check [BSPure](https://github.com/JessPinkman/BSPure), an extension to use Bootstrap components.**

# How

There are two main classes:

## Pure\Component

    This is the class that abstracts an html markup element
    you combine them to create your html
    use magic calls to define the markup attributes

## Pure\Pure

    This is the factory, use static magic calls to create components.
    You can then chain magic calls on the component to create the component attributes

### example:

```php

use Pure\Pure;

echo Pure::h3()
    ->id('my-first-pure-component')
    ->class('badge badge-primary')
    ->my_custom_prop('pure')
    ->___('Hello World!');

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
    ->___(
        Pure::p()
            ->class('inner')
            ->___("I'm nested !")
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

//Create a select component, to spped up its creation
//Some magic to add the name attribute, as well as class for children

class Select extends Component {

    function __construct( string $name, ...$children )
    {
        parent::__construct( 'select' );    //create component with 'select' markup
        $this
            ->class('form__select')         //add class attribute = 'form__select'
            ->name( $name )                 //assign attribute name with passed $name in constructor
            ->___($children);            //append children
    }
}

class Option extends Component
{

    public function __construct( string $label, $value = null, bool $disabled = false )
    {
        parent::__construct('option');      //create component with 'option' markup
        $this
            ->class('form__option')         //add class attribute = 'form__option'
            ->value($value ?? false)        //add value attribute if it is defined, otherwise no value attribute
            ->disabled($disabled)           //add disabled attribute if necessary
            ->___($label);               //append label inside in inner html
    }
}

echo Pure::form()
    ->id('select-form')
    ->___(
        new Select('user-preference', [
            new Option( 'Select your preference', null, true ),
            new Option( 'A is the best', 'a' ),
            new Option( 'B is better', 'b' ),
            new Option( 'C is amazing', 'c' )
        ]);
    );

```

###### output

```html
<form id="select-form">
  <select name="user-preference" class="form__select">
    <option class="form__option" disabled>Select your preference</option>
    <option class="form__option" value="a">A is the best</option>
    <option class="form__option" value="b">B is better</option>
    <option class="form__option" value="c">C is amazing</option>
  </select>
</form>
```

### Advanced example

let's create different reusable components (views)

- default Page view
- grid view
- product view

```php

//default page view, already includes head, header and footer views, we just need to pass the main content
class DefaultPageView extends Component
{
    public function __construct(...$inner_content)
    {
        parent::__construct('html');
        $this->___(
            new PageHead(),
            Pure::body(
                new HeaderView(),
                Pure::main($inner_content),
                new FooterView()
            )
        );
    }

    // Overwrite __toString in order to include doctype at beginning of the page
    public function __toString(): string
    {
        return '<!DOCTYPE html>' . parent::__toString();
    }
}

//reusable component to create an html grid, and assign a class to all grid children
class GridView extends Component
{
    public function __construct(...$tiles)
    {
        parent::__construct('div');
        $this
            ->id('grid')
            ->___($tiles);
    }

    //overwrite ___ method in order to abstract a grid specific structure, each child is a div with a specific class
    public function ___(...$children): self
    {
        foreach ($children as $child) {
            parent::___(
                Pure::div()
                    ->class('grid_child')
                    ->___($child)
            );
        }
    }
}

//reusable component to render a single product tile html
class ProductView extends Component
{

    public function __construct(array $product)
    {
        parent::__construct('article');             // create component with article markup
        $this
            ->class('product__tile')                //add class
            ->data_product_id($product['id'])       //add custom attribute
            ->___(
                Pure::h1()                          //first child, the title
                    ->class('product__tile_title')
                    ->___($product['name']),
                Pure::img()                         //second child, the image
                    ->class('product__tile_img')
                    ->src($product['imgURL'])
                    ->alt($product['name']),
                Pure::span()                        //third child, the price
                    ->class('product__tile_price tag')
                    ->class($product['promotion'] ? 'product__tile_price--promotion' : 'product__tile_price--no-promotion') //conditionally load class
                    ->___("USD $product->price"),
            );
    }
}



//to use inside your view controller
$products = Product::getProductList();
$product_views = array_map(fn ($single) => new ProductView($single), $products);

echo new DefaultPageView(new GridView($product_views));

```

###### output

```html
<!DOCTYPE html>

<html>
  <head>
    ...
  </head>
  <body>
    <header>...</header>
    <main>
      <div class="grid">
        <div class="grid_child">
          <article class="product__tile" data-product-id="3">
            <h1 class="product__tile_title">Blue Sweater</h1>
            <img
              class="product__tile_img"
              src="/uploads/product-3.jpg"
              alt="Blue Sweater"
            />
            <span
              class="product__tile_price tag product__tile_price--promotion"
            >
              USD 39
            </span>
          </article>
        </div>
        <div class="grid_child">
          <article class="product__tile" data-product-id="7">
            <h1 class="product__tile_title">Red Dress</h1>
            <img
              class="product__tile_img"
              src="/uploads/product-7.jpg"
              alt="Red Dress"
            />
            <span
              class="product__tile_price tag product__tile_price--no-promotion"
            >
              USD 59
            </span>
          </article>
        </div>
        <div class="grid_child">
          <article class="product__tile" data-product-id="17">
            <h1 class="product__tile_title">Grey Fedora</h1>
            <img
              class="product__tile_img"
              src="/uploads/product-17.jpg"
              alt="Grey Fedora"
            />
            <span
              class="product__tile_price tag product__tile_price--no-promotion"
            >
              USD 40
            </span>
          </article>
        </div>
      </div>
    </main>
    <footer>...</footer>
  </body>
</html>
```

And build from here even more complexe structures...
