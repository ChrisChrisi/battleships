<?php

// class that manages the visualization of the game
class BoardVisualizer
{
    public static function stringifyBoard($board, $displayMode)
    {
        return $displayMode === 'play' ? self::showPlayBoard($board) : self::showRemainingShips($board);
    }

    /**
     * Show remaining ships parts on the game board
     * @param $board - game board
     * @return string - game board as string
     */
    private static function showRemainingShips($board)
    {
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= SPACE.SPACE.$i;
        }
        $string .= NEW_LINE;
        foreach ($board as $index => $row) {
            $string .= $index.SPACE;
            foreach ($row as $cell) {
                if (!is_null($cell['ship']) && $cell['symbol'] != HIT_SYMBOL) {
                    $string .= 'X'.SPACE.SPACE;
                } else {
                    $string .= SPACE.SPACE.SPACE;
                }
            }
            $string .= NEW_LINE;
        }
        return $string;
    }

    /**
     * Show the game board as string
     * @param $board
     * @return string
     */
    private static function showPlayBoard($board)
    {
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= SPACE.SPACE.$i;
        }
        $string .= NEW_LINE;

        foreach ($board as $index => $row) {
            $string .= $index.SPACE;
            foreach ($row as $cell) {
                $string .= $cell['symbol'].SPACE.SPACE;
            }
            $string .= NEW_LINE;
        }
        return $string;
    }

}