# MySteemitFriends

A tool to analyse your upvote statistics for Steemit. Go to live website at http://mysteemitfriends.online to see how it works. Each file in this project will be explained in detail below.

## Files

- [**index.php**](https://github.com/Bulletproofmonk/MySteemitFriends/blob/master/index.php): The main page for the tool. Enter your user name and click the button to see the statistical results who has upvoted you as well as who you have upvoted in table for a side by side comparison. You can then set filters and ordering methods with dropdowns and textboxes (e.g. time period, whether to exclude comment upvotes, rank by number of votes, total weight or total weight * Steem Power). 

  Alternatively, you may click another button to find out your ranking in terms of number of followers.

- [**upvotelist.php**](https://github.com/Bulletproofmonk/MySteemitFriends/blob/master/upvotelist.php): A page opened up from index.php. It will show all articles voted on by a particular user, with the date and percentage of each vote, and a show ranking button that displays this user's contributation as a ranking against all other users who voted. It also includes a built in calculator for you to add up contributions by a specific user.

- [**style.css**](https://github.com/Bulletproofmonk/MySteemitFriends/blob/master/style.css): CSS Stylesheet for the whole website.

- [**followers.php**](https://github.com/Bulletproofmonk/MySteemitFriends/blob/master/followers.php): A list of Steemit Users ranked by number of followers. 50 users per page - any page can be selected and retrieved. A search box can also be used to locate a particular user.

- [**get_follower_rank.php**](https://github.com/Bulletproofmonk/MySteemitFriends/blob/master/followers.php): PHP file that queries SteemSQL to find out the ranking of a paritcular user in terms of number of followers, and then allows the user to jump to that particular page on followers.php.



## Folders

- [**bootstrap**](https://github.com/Bulletproofmonk/MySteemitFriends/tree/master/bootstrap): The bootstrap framework imported in this website.
- [**jquery**](https://github.com/Bulletproofmonk/MySteemitFriends/tree/master/jquery): The jquery library imported in this website.
- [**extensions**](https://github.com/Bulletproofmonk/MySteemitFriends/tree/master/extensions): Any extensions imported in this website (e.g. popovers).
- [**images**](https://github.com/Bulletproofmonk/MySteemitFriends/tree/master/images): Any images used in this website.
