# cakedropdown-list
ver 2.6

Dynamic Drop Down Menus with CakePHP 2.x
The Problem

I was working on a billing software platform to manage my client billing and project management, and it is broken down into tasks that belong to projects that belong to clients. You can go into the client and add the project and then into the project to add the task (which passes the project id and client id), so it wouldn’t be a ‘big’ deal to have to follow this to add a task for a specific project/client. However, I wanted the functionality to work both ways, to allow a user to add a task and select the client and project without having to go into the project or client first (less clicks/easier when your adding a bunch of tasks for a bunch of projects).

On the add a task page I have a drop down of all the clients (which with CakePHP is super easy and generated with the bake function automatically if you go that route) and then a drop down of all the projects (again generated easily) so you select the client, then the project, then insert the task description and other info and save it. Doesn’t sound like a problem in theory- except that the projects are all listed even if they don’t go with the client selected. So I can assign a task to a project that doesn’t belong to the client selected. Yes, I could  validate the info once it is submitted and give an error not saving the task if the project doesn’t belong to the client, but that is a waste of time (and let’s face it- rejection is no fun).

What I need is to dynamically generate the options for the project drop down with only the projects that belong to the client selected in the first drop down. DUH. That sounds simple, however, I am not real savvy with JavaScript, I can edit the heck out of it raw, I can even write it from scratch if I use my references, but I am not an expert. I also had no idea how to deal with it in the CakePHP framework (with the Js helper). I am savvy with PHP, I can write that in my sleep (and I do LOL) but I am still getting used to CakePHP, so sometimes I need things simplified for me (idiot proof). I come from a procedural PHP background, so I am jumping into Object Orientated PHP and CakePHP framework all at the same time. Yes, my sanity has been questioned, the verdict is still out.
The Process

    Days. Yes DAYS is what it took for me to finally get this working. I have bald spots now from yanking my hair out over this…well ok, not really, but only because I like my hair too much.

Okay I started this endeavor like I do any…I Googled that shit. (excuse my language- I swear a lot…) I found pages of links of course, but the pickings were slim for content that actually was relevant (for CakePHP version 2.x) and was a full answer. There were a bunch of StackOverflow results but generally they are too specific for a general ‘How do I?’ kind of question. I did find a few ‘tutorials’ like this one, this one and this one.  The first I ruled out right away because it was using var $helpers = array(‘Ajax’);  which wasn’t even valid anymore. The second one I ruled out pretty quickly when the link to the CakePHP article didn’t work and it was referencing 1.3, I figured it wouldn’t work. The third option looked promising though, so that is where I dug in, and ultimately figured out (with a few tweaks).

I tried to take the example given in this tutorial and modify it to fit my needs (using CakePHP 2.5.4 and CakePHP 2.5.5). It was an epic fail. I got it ‘kind of’ working once but when I selected a client it put the client list in the project drop down (no clue how I did that, and I can’t replicate it- but you will find that is a talent of mine). Then I figured I would start troubleshooting the problem from the base, see if the error was at the tutorial level or mine (I was betting on it being mine, but it turns out is was a little of both). My first step was to do the tutorial step by step and see if I could get that working following the directions exactly. It didn’t work at first, then I read through the comments trying each suggestion/correction until I finally had success!

You can check out the example working here: http://dynamic.marnienickelson.com/ Don’t try to save/edit/delete things- it won’t work (and it’s rude). Basically this is the tutorial with a few modifications (which I am going to go over). Since I have no control over the blog it came from I am going to go over all the steps from start to finish just in case it no longer works or it gets replaced by porn or something (not that there is anything wrong with that- but let’s face facts, if you are on the internet and found this post looking for porn, you’re doing it wrong).
The Solution

So I am sure by now you are asking “Is she EVER going to tell us how she did it?” Of course I am, but how entertaining would this be if I just got to the answers? Yeah entertainment might not be what you’re looking for, but it keeps me from getting bored, which would keep me from posting which would mean you wouldn’t have the answer at all so suck it up buttercup and deal ;-). These steps assume you are familiar with CakePHP and baking a project, if you’re you should start there, then come back here k?

    This solution uses CakePHP 2.5.5 in the example, but my project is using CakePHP 2.5.4 with no problems. It also uses the Js Helper and the jQuery 1.11.1 library (Google Hosted), however it has also been tested with the jQuery 2.1.1 library (also Google Hosted) with no problems (the difference in 1.11.1 and 2.1.1 is the support of IE 8 mostly).

Step 1: Create an example database

Create a database using this database schema. It is a text file but you can save it as a .sql file and import it into a blank database. Or you can run the following in your MySQL database:

CREATE TABLE `categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
 
INSERT INTO `categories` (`id`, `name`)
VALUES
	(1,'books'),
	(2,'music'),
	(3,'electronics');
 
CREATE TABLE `posts` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(50) DEFAULT NULL,
  `subcategory_id` INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
);
 
INSERT INTO `posts` (`id`, `title`, `subcategory_id`)
VALUES
	(1,'The title',1),
	(2,'A title once again',4),
	(3,'Title strikes back',7);
 
CREATE TABLE `subcategories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT(10) UNSIGNED DEFAULT NULL,
  `name` VARCHAR(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
 
INSERT INTO `subcategories` (`id`, `category_id`, `name`)
VALUES
	(1,1,'fiction'),
	(2,1,'biography'),
	(3,1,'children'),
	(4,2,'classical'),
	(5,2,'rock'),
	(6,2,'jazz'),
	(7,3,'camera'),
	(8,3,'audio'),
	(9,3,'tv');

Either way you get a database with three tables- Categories, Posts and Subcategories with a few entries in each.

    This isn’t how I would set up a category/subcategory feature in a real program I was developing, but this is the example that was given in the tutorial and for our purposes here, it does work.

Step 2: Run the cake bake all on all three tables

    In the tutorial they suggested that you bake the 3 Models and then a controller for the Post Model with default CRUD functions (add, edit, view, index) along with views for the Post Controller. They also go on to state that for the Subcategories Controller you can just create it with one function to get the data via AJAX.

    I tried to do it this way first, I really did. I don’t know why I didn’t have luck doing it this way, but in the comments miglos mentioned that he had to bake the models through the command prompt twice but it worked. So with that thought in my head I re-baked the the Models only with no luck. I had the thought at this point that maybe I was answering one of the prompts wrong. When you bake just a model with the cake bake function it asks you a bunch of things; I was in a hurry and frustrated and probably wasn’t paying enough attention to these, so this could be entirely my error.

    I didn’t really feel like doing it a third time and paying more attention to the prompts so I just did a cake bake all on all three tables. I am sure this added more to the program that was needed for the example, but really so what?

Step 3: Set up your program to use the CakePHP Js helper

First, in the Posts Controller (app/Controller/PostsController.php) after the class PostsController extends AppController { you need to add:

public $helpers = array('Js');

Next, in your layout (app/View/Layouts/default.ctp) you need to call the JavaScript library you want to use and include the writeBuffer for the Js helper or it won’t work. Before the closing body tag (</body>) you want to include this:

<?php echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
echo $this->Js->writeBuffer(); ?>

    In this area I veered off of the tutorial a bit, they mentioned the $helpers call at the very beginning and then changes to the default layout at the end, but both are what you need to set up your program to use the Js helper, so I included them here, that way it’s all set up and when you are writing the function to get the content or the script to call the function you won’t have to worry about the actual helper not working.

    In the tutorial they included <script type=”text/javascript” src=”http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js”></script>  along with the writeBuffer(); call at the bottom of the default layout. I used the CakePHP way to include the most current jQuery library (from Google Hosted Library) instead.  The tutorial also shows a call to $scripts_for_layout which is deprecated (starting in CakePHP 2.1) and is replaced with HtmlHelper’s script method (if you baked your whole project as instructed in Step 2 your default layout will already include things that look like this: echo $this->fetch(‘script’);).

Step 4: Write your function to get your data

In this example we are getting the data from the Subcategories table, which means the function should go in the SubcategoriesController.php (app/Controller/SubcategoriesController.php) after the class SubcategoriesController extends AppController{.

    Remember in the tutorial it didn’t have you baking all the tables, and it mentioned this could be the only function (they say action, but they are functions and they are declared as functions so I call them functions because this crap is confusing enough) in the Subcategories Controller, but we (okay *I*) baked the whole thing, so there will be other functions in this file.

    In a ‘real’ program you would want the other functions anyway (so you can add/edit/view and delete the subcategories), so you can just add this function to get the subcategories by category to the others, it really doesn’t matter if you put it first or last or somewhere in the middle, as long as it is after the class declaration and before the closing }.

So here is the function that will get the data for the subcategories dropdown dynamically:

public function getByCategory() {
     $category_id = $this->request->data['Post']['category_id'];
     $subcategories = $this->Subcategory->find('list', array(
          'conditions' => array('Subcategory.category_id' => $category_id),
          'recursive' => -1));
     $this->set('subcategories', $subcategories);
     $this->layout = 'ajax';
}

    In the comments someone mentioned if you are using the default layout for your project you need to include $this->layout = false to this function however this example already has a layout declaration (maybe the blogger changed it after the comment- idk) so you don’t need to include that, this works just fine the way it is. That being said, you can change the $this->layout = ‘ajax'; to $this->layout = false; and the function still works, so do it either way, but you MUST have the layout declared as false or as ‘ajax’ for it to function. If you look at the ajax layout (app/View/Layouts/ajax.ctp) you will see there is nothing in it but this:

    <?php echo $this->fetch('content'); ?>

    The default layout however has a ton of stuff in it (remember, we edited it in Step 1?) which would also be rendered if you didn’t declare the layout as either off (which would just pass the info) or as ‘ajax’ which also just passes the info.

Step 5: Create your view for the getByCategory function

You need to create the file get_by_category.ctp view and place it in the Subcategories view folder (app/View/Subcategories/get_by_category.ctp). Then you need to write a very simple view that will output the options portion of the form element.

<?php foreach($subcategories as $key => $value): ?>
<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
<?php endforeach; ?>

    Now this is a very simple view file, yes, but this is also where my big mistake was when I tried to adapt this to the program I am actually working on. Yes that is like admitting I failed the coloring part of Kindergarten but this is pretty much me, I can handle the super complicated crap and figure out all the major stuff and it is a misspelled variable, or missing semi-colon that breaks my world. What can ya do?

    So don’t make the same mistake I did, if you change the variable name from Step 4 in your getByCategory() function to something other than $subcategories then you must change the variable name in the foreach in this view file. (Yeah DUH. Stop making fun of me, I am helping you! lol)

Step 6: Edit the controller that your view uses

    This is a step that the tutorial completely skipped over and why my example wouldn’t work at all at first… maybe they thought it was a simple/obvious thing that someone should already know, but I am one for ‘idiot proofing’ so I am including it.

With the straight up baked controller for your posts (app/Controller/Posts/PostsController.php) your add function would look like this:

    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create();
            if ($this->Post->save($this->request->data)) {
                $this->Session->setFlash(__('The post has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The post could not be saved. Please, try again.'));
            }
        }
        $subcategories = $this->Post->Subcategory->find('list');
        $this->set(compact('subcategories'));
    }

Can anyone see what the problem is for what we want to do in the add view for posts? We are going to be putting 3 inputs into the view (title, category_id and subcategory_id). Two of those three things are going to be drop down lists that need to be filled with something. The subcategories list is the one we are trying to generate the filling for, so we really don’t have to worry about that one (although with this code it will be filled with everything when the page loads). So what are we missing? anyone? Bueller? Bueller? anyone? (ok, I get props for the Ferris Bueller reference…)

What is our category_id drop down going to be filled with? Where are those options going to come from? CakePHP is smart, yes, but not THAT smart, there is nothing connecting the posts to the categories, so it didn’t write the code to get the list that would fill the drop down. So you need to tell it to build that list so the categories are filled, so you can select something, and then allow this great code you’re about to write to filter the subcategories. In order to generate a list of categories you need to put in the following code:

$this->set('categories', $this->Post->Subcategory->Category->find('list'));

Or if you want to write it the way the bake does it you can do it this way:

$subcategories = $this->Post->Subcategory->find('list');
$categories = $this->Post->Subcategory->Category->find('list');
$this->set(compact('subcategories','categories'));

    Either way will work, but if you do it the way bake does it, make sure you don’t forget to add the ‘categories’ to the set call to set the variable.

    I generally try and write my variables the way I showed you first (with the set and call in the same line) that way I don’t have to worry about a typo or changing a variable in multiple places if I rename it. Just saves a bit of time.

    Also note that since the posts aren’t directly related to the categories you have to go through the subcategories to get the list. If there were a direct connection in the database, not only would bake have already written the connection, but you could skip the ->Subcategory part of the call. Of course there are all kinds of reasons and theories why it is better or worse to do things one way or the other and I already mentioned this isn’t how I would handle categories/subcategories anyway.

Step 7: Edit your view to include the right form elements

With a straight baked view for the add function (app/View/Posts/add.ctp) in the Posts Controller you will see the following:

    <?php
        echo $this->Form->input('title');
        echo $this->Form->input('subcategory_id');
    ?>

    This is because in our database (which is what the bake function uses to decide what form elements are required for the add/edit views) table for posts only has a subcategory_id so it has no way of knowing you would want to put in a category_id (because it isn’t going into the database- it is just being used to slim down the subcategory options).

You need to add the input for the category_id so the user can select it, then we can send it off to get the subcategories that are related. Where you want the category selection drop down add this:

echo $this->Form->input('category_id');

It needs to be between the opening and closing php tags (<?php ?>) within the form create (<?php echo $this->Form->create(‘Post’); ?>) and form end (<?php echo $this->Form->end(__(‘Submit’)); ?>) lines.

    In the tutorial it tells you to add the element and change the order or the elements so they are like this:

         echo $this->Form->input('category_id');
         echo $this->Form->input('subcategory_id');
         echo $this->Form->input('title');

    In reality it doesn’t matter what order the elements are in, even if they subcategory selection drop down is first, it will still work just fine. That being said, I have no idea why anyone would think that was a logical choice, considering user experience, but hey- you do your ‘thang’. I thought it made the most sense to put the title first, then select the category and subcategory so mine looks like this:

        echo $this->Form->input('title');
        echo $this->Form->input('category_id', array('empty' => '-Pick Category-'));
        echo $this->Form->input('subcategory_id', array('empty' => '-No Category Selected-'));

Step 8: Write the code to update the dropdown

This is where those of you familiar with straight writing JavaScript and jQuery will wonder what kind of hocus pocus this is. That is pretty much the way those of us familiar with procedural PHP felt when we looked at OOP/CakePHP for the first time. At the bottom of the view file you were just editing (app/View/Posts/add.ctp) you will be using the Js helper to generate the jQuery. This is done with the following code:

$this->Js->get('#PostCategoryId')->event('change', 
    $this->Js->request(array(
        'controller'=>'subcategories',
        'action'=>'getByCategory'
        ), array(
        'update'=>'#PostSubcategoryId',
        'async' => true,
        'method' => 'post',
        'dataExpression'=>true,
        'data'=> $this->Js->serializeForm(array(
            'isForm' => true,
            'inline' => true
            ))
        ))
    );

    Remember this is technically PHP so it must go between <?php and  ?> tags. Yeah I know, it is generating a script- but PHP is what generates it using the Js Helper within CakePHP so wrap it in the PHP tags, not <script></script> tags, when it is generated on the live side the PHP will wrap it in the script tags for you. (nifty huh?)

    Now I will explain what is happening here with the Js Helper. We are asking it to get the element with the id PostCategoryId:

    $this->Js->get('#PostCategoryId')

    Watch it for change:

    event('change',

    When it does change we send the info to the controller ‘subcategories’ to the action(function) ‘getByCategory':

    $this->Js->request(array(
            'controller'=>'subcategories',
            'action'=>'getByCategory'
            ),

    Once it returns whatever that function does (remember- it sends category_id to the database to get the subcategories with only that category_id and puts it in that nifty view with the foreach loop and returns it with nothing else in the ‘ajax’ layout (or no layout if you used the $this->layout = false;)) it updates the element on the page with the id PostSubcategoryId.

    array(
            'update'=>'#PostSubcategoryId',

    We are telling it not to hold up the rest of the page loading on this script:

    'async' => true,

    We tell it what method to retrieve the data (it defaults to ‘get’ so it must be declared as ‘post’):

    'method' => 'post',

    Then we let it know the data is going to be needed as an expression (so the data is filled into the form and can be submitted to the database):

    'dataExpression'=>true,

    Finally we make sure we take the data from the form and send it along with the request:

    'data'=> $this->Js->serializeForm(array(
                'isForm' => true,
                'inline' => true

Finally…the end ramblings

Remember, I included the working example of this for you to check out: http://dynamic.marnienickelson.com/. That means, at the time of this writing, this tutorial did work. The tutorial I worked with is almost functional (remember this one- that hopefully still isn’t porn), I just added step 6 plus a bunch of detail, explanations and re-organized it a bit so it makes more sense to me. Making sense to myself is the main purpose I write these types of things. Comment if you have any problems or find any errors (hey they happen!).

    Now that this huge PITA (pain in the ass) tutorial is written and functional and I have solved my ‘problem’ and can reliably replicate it in other areas of the programs I am working on, the only thing that could possibly happen would be that CakePHP is removing the JsHelper from the new version of CakePHP (3.x). Ahhhh such is life right? That is ok though, we will still be using the other helpers to interact with JavaScript libraries and the writing will be similar I am sure. Actually I am hoping it will be much easier (from the docs I have read that is where they are headed anyway), maybe next time I will only need 1 day to pull my hair out instead of multiple!

A few additional tips & tricks…FYI

Okay if you actually have made it to the bottom of this, seemingly never ending post, bravo. Really, I am impressed. I just wanted explain a few extra things some have noticed.

“It says “-Pick Category-” in the category drop down and “-No Category Selected-” in the subcategory selection box when you load the example, but mine doesn’t!” – Yes this is true, if you follow this tutorial exactly your result will show the first option in the category dropdown and nothing in the subcategory dropdown. I added an ‘empty’ selector to both of the inputs. In the add view for posts (app/View/Posts/add.ctp) I changed the following code:

echo $this->Form->input('category_id');
echo $this->Form->input('subcategory_id');

To this:

echo $this->Form->input('category_id', array('empty' => '-Pick Category-'));
echo $this->Form->input('subcategory_id', array('empty' => '-No Category Selected-'));

That simply allows the empty value to say something. If your selection is required it won’t validate as filled in so it will still kick out if you don’t select something so no worries there.

“Your example doesn’t show the subcategories in the dropdown list when the page is loaded, mine shows them all.” – Yes true again. Logically I didn’t think it was required to have ALL the subcategories listed in the drop down (even though I did put the empty option there too) on the page load. It isn’t a big deal either way, but I prefer to only give the options once the category is selected. People will do things out of order, they will mess with crap (and some evil ones will try and break your stuff), I figure less to play with the better. So if you go back to the posts controller (app/Controller/PostsController.php and look at the add function just remove these line:

$subcategories = $this-> Post-> Subcategory-> find ('list');
$this-> set (compact ('subcategories'));

    In Step 6 if you opted to write the code the second way (the way bake writes it) then don’t remove the second line just change it to this:

    $this-> set (compact ('categories'));

    The way you are still setting the categories variable (so you won’t break it). I guess that is just one more reason to do Step 6 the first way… lol

“Your amazing, I love you, will you marry me and have my techie babies?” – Nope sorry. I agree I am amazing ( LOL tongue firmly in cheek) but check my about me page, I am happily married to the love of my life (who is a non-techie if you can believe it) and I already have 4 boys which is all the children I am destined to have.  I am sure you’re amazing too and will find the person of your dreams…but remember you have to get off the computer sometimes, there are actually people who don’t live on them! (shocking but true!)

Tags: CakePHP, JavaScript, jQuery, PHP
