<?php
function js_lang()
{
    echo '
    <script type="text/javascript">
        var wpfs={
            "countdown" : "'.__(": : : : day week month year decade century millennium", "fantasy-sports").'",
            "countdown1" : "'.__(": : : : days weeks months years decades centurys millenniums", "fantasy-sports").'",
            "pleasewait" : "'.__("Loading...Please wait!", "fantasy-sports").'",
            "Name" : "'.__("Name:", "fantasy-sports").'",
            "Entry Fee" : "'.__("Entry Fee: $", "fantasy-sports").'",
            "Prizes" : "'.__("Prizes: $", "fantasy-sports").'",
            "Prize Structure" : "'.__("Prize Structure:", "fantasy-sports").'",
            "Creator" : "'.__("Creator:", "fantasy-sports").'",
            "Sport" : "'.__("Sport:", "fantasy-sports").'",
            "Game Type" : "'.__("Game Type:", "fantasy-sports").'",
            "Start" : "'.__("Start:", "fantasy-sports").'",
            "End1" : "'.__("End:", "fantasy-sports").'",
            "End2" : "'.__("Prizes paid next day", "fantasy-sports").'",
            "no_entry_yet" : "'.__("This game doesn\'t have any entries yet", "fantasy-sports").'",
            "Normal" : "'.__("Normal:", "fantasy-sports").'",
            "Playerdraft:" : "'.__("Playerdraft:", "fantasy-sports").'",
            "info" : "'.__("Info", "fantasy-sports").'",
            "fixture" : "'.__("Fixture", "fantasy-sports").'",
            "scoring" : "'.__("Scoring", "fantasy-sports").'",
            "set_pick" : "'.__("Please set your picks.", "fantasy-sports").'",
            "cut_date_expired" : "'.__("Sorry, you can\'t save your picks for this pool because the cut date is expired.", "fantasy-sports").'",
            "see_pick_after_league_start" : "'.__("You can see another user\'s picks after league start only.", "fantasy-sports").'",
            "fight_no_result" : "'.__("No results for this fight", "fantasy-sports").'",
            "cant_display_pick" : "'.__("Cannot display picks.", "fantasy-sports").'",
            "h_User" : "'.__("User", "fantasy-sports").'",
            "h_Rank" : "'.__("Rank", "fantasy-sports").'",
            "h_Points" : "'.__("Points", "fantasy-sports").'",
            "h_Winners" : "'.__("Winners", "fantasy-sports").'",
            "h_Methods" : "'.__("Methods", "fantasy-sports").'",
            "h_Rounds" : "'.__("Rounds", "fantasy-sports").'",
            "h_Minutes" : "'.__("Minutes", "fantasy-sports").'",
            "h_Bonuses" : "'.__("Bonuses", "fantasy-sports").'",
            "h_Winnings" : "'.__("Winnings", "fantasy-sports").'",
            "fight_no_result" : "'.__("No results for this fight", "fantasy-sports").'",
            "fullpositions1" : "'.__("Player cannot be added - all", "fantasy-sports").'",
            "fullpositions2" : "'.__(" positions are filled", "fantasy-sports").'",
            "players_out_team" : "'.__("Are you sure you want to clear all players from your team?", "fantasy-sports").'",
            "player_each_position" : "'.__("Please select a player for each position", "fantasy-sports").'",
            "team_exceed_salary_cap" : "'.__("Your team has exceeded this game\'s salary cap. Please change your team so it fits under the salary cap before entering", "fantasy-sports").'",
            "player_no_match" : "'.__("This player has not played any matches yet.", "fantasy-sports").'",
            "pick_a_team" : "'.__("Pick a team of players from the following games:", "fantasy-sports").'",
            "pick_player_from_list" : "'.__("Pick players from the following lists", "fantasy-sports").'",
            "no_contest_entry" : "'.__("There are no entries in this contest yet.", "fantasy-sports").'",
            "input_picks" : "'.__("Please enter your picks", "fantasy-sports").'",//submitpicks.js
            "sec_warn1" : "'.__("Please logout and login again. A security warning ", "fantasy-sports").'",//withdraw.js
            "sec_warn2" : "'.__("has been detected. Please contact support@fanvictor.com if issue persists", "fantasy-sports").'",
            "number_decimal" : "'.__("Number can only be 2 decimal places at most", "fantasy-sports").'",
            "valid_amount" : "'.__("Please enter a valid withdrawal amount", "fantasy-sports").'",
            "withdraw_amount" : "'.__("Please enter an amount to withdrawal", "fantasy-sports").'",
            "invalid_amount" : "'.__("Entered amount is greater than your balance. Please re-enter an amount less than $", "fantasy-sports").'",
            "invalid_email" : "'.__("Please enter a valid email address", "fantasy-sports").'",
            "a_sure" : "'.__("Are you sure?", "fantasy-sports").'",
            "a_sb_organization" : "'.__("--Please select organization first--", "fantasy-sports").'",
            "a_name" : "'.__("Name", "fantasy-sports").'",
            "a_points" : "'.__("Points", "fantasy-sports").'",
            "a_prizes" : "'.__("Prizes", "fantasy-sports").'",
            "a_awarded" : "'.__("Awarded", "fantasy-sports").'",
            "a_fee" : "'.__("Entry Fee", "fantasy-sports").'",
            "a_size" : "'.__("Size", "fantasy-sports").'",
            "a_entries" : "'.__("Entries", "fantasy-sports").'",
            "a_total" : "'.__("Total Cash", "fantasy-sports").'",
            "edit" : "'.__("Edit", "fantasy-sports").'",
            "enter" : "'.__("Enter", "fantasy-sports").'",
            "free" : "'.__("Free", "fantasy-sports").'",
            "position" : "'.__("Position", "fantasy-sports").'",
            "salary_cap" : "'.__("Salary Cap", "fantasy-sports").'",
            "contest" : "'.__("Contest", "fantasy-sports").'",
            "scoring_categories" : "'.__("Scoring Categories", "fantasy-sports").'",
            "view" : "'.__("View", "fantasy-sports").'",
            "close" : "'.__("Close", "fantasy-sports").'",
            "send" : "'.__("Send", "fantasy-sports").'",
            "cancel" : "'.__("Cancel", "fantasy-sports").'",
            "from" : "'.__("From", "fantasy-sports").'",
            "to" : "'.__("To", "fantasy-sports").'",
            "add" : "'.__("Add", "fantasy-sports").'",
            "delete" : "'.__("Delete", "fantasy-sports").'",
            "summary" : "'.__("Summary", "fantasy-sports").'",
            "game_log" : "'.__("Game Log", "fantasy-sports").'",
            "player_news" : "'.__("Player News", "fantasy-sports").'",
            "salary" : "'.__("Salary", "fantasy-sports").'",
            "played" : "'.__("Played", "fantasy-sports").'",
            "season_statistics" : "'.__("Season Statistics", "fantasy-sports").'",
            "latest_player_news" : "'.__("Latest Player News", "fantasy-sports").'",
            "played" : "'.__("Played", "fantasy-sports").'",
            "remove_player" : "'.__("Remove Player", "fantasy-sports").'",
            "next_game" : "'.__("Next Game", "fantasy-sports").'",
            "updating" : "'.__("Updating", "fantasy-sports").'",
            "no_news" : "'.__("No news", "fantasy-sports").'"
        }
    </script>';
}

add_action('wp_head','js_lang');

add_action('admin_enqueue_scripts', 'js_lang');