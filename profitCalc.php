<?php

/**
 * Calculate Totals and Profit Margins from CSV file.
 *
 * Provides Average Cost, Average Price, Total Quantity, Average Profit Margin and Total Profit
 */

// !!! SETTINGS !!!
// declare path and file to be used!
$file = 'products.csv';


/**
 * Converts MultiDimensional Array to formatted array for easy display and manipulation in view
 *
 * Requires a CSV file with a header row.
 * Returns a structured multi-dimensional array similar to:
 * [
 *   ['sku'] => value
 *   ['price'] => value
 *   etc
 * ]
 * @param $fileData
 * @return array
 */
function read_data( $fileData )
{

    /**
     * Array is Multi Dimensional
     * Process array to identify headers and then data
     */
    foreach( $fileData as $key => $value )
    {
        if( $key === 0 ) // Header row
        {
            foreach( $value as $k => $v )
            {
                switch ( $v )
                {
                    case 'sku':
                        $columns[ 0 ] = 'sku';
                        break;
                    case 'price':
                        $columns[ 1 ] = 'price';
                        break;
                    case 'qty':
                        $columns[ 2 ] = 'qty';
                        break;
                    case 'cost':
                        $columns[ 3 ] = 'cost';
                        break;
                    default:
                        break;
                }
            }
        } else {
            foreach( $value as $k => $v )
            {
                $row[ $columns[ $k ] ] = $v;
            }
            $data[] = $row;
        }
    }

    return $data;
}

/**
 * Pull in CSV file "products.csv"
 * !!! First row expected to contain Header with columns "sku", "price", "qty", "cost"
 * !!! incorrectly formatted files will not yield expected results
 *
 * Store values into array
 */

$fileData = array_map( 'str_getcsv', file( $file ) );
$data = read_data( $fileData );

setlocale(LC_MONETARY, 'en_US.UTF-8'); // for formatting dollar values

?>

<html>
<head>
    <style>
        body {
            background-color: black;
            color: white;
        }
        .negative {
            color: red;
        }
        .positive {
            color: darkgreen;
        }
        td, th {
            padding: 10px;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Cost</th>
                <th>Price</th>
                <th>QTY</th>
                <th>Profit Margin</th>
                <th>Total Profit</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ( $data as $row ): ?>
            <tr>
                <td><?php
                    echo $row[ 'sku' ];
                    ?>
                </td>
                <td><?php
                    $costs[] = $row[ 'cost' ];
                    echo $row[ 'cost' ];
                    ?>
                </td>
                <td><?php
                    $prices[] = $row[ 'price' ];
                    echo $row[ 'price' ];
                    ?>
                </td>
                <td><?php
                    $qtys[] = $row[ 'qty' ];
                    ?>
                    <span class="<?php echo $row[ 'qty' ] < 0 ? 'negative' : 'positive' ;?>"><?php echo money_format( '%.2n', $row[ 'qty' ] ); ?></span>
                </td>
                <td><?php
                    $margin = $row[ 'price' ] - $row[ 'cost' ];
                    $margins[] = $margin;
                    ?>
                    <span class="<?php echo $margin < 0 ? 'negative' : 'positive' ;?>"><?php echo money_format( '%.2n', $margin ); ?></span>
                </td>
                <td><?php
                    $profit = $row[ 'qty' ] * $margin;
                    $profits[] = $profit;
                    ?>
                    <span class="<?php echo $margin < 0 ? 'negative' : 'positive' ;?>"><?php echo money_format( '%.2n', $profit ); ?></span>
                </td>
            </tr>
        <?php endforeach; ?>
            <tr>
                <td></td>
                <td><?php
                    echo money_format( '%.2n', array_sum( $costs ) / count( $costs ) );
                    ?> avg
                </td>
                <td><?php
                    echo money_format( '%.2n', array_sum( $prices ) / count( $prices ) );
                    ?> avg
                </td>
                <td><?php
                    echo array_sum( $qtys );
                    ?>
                </td>
                <td><?php
                    echo money_format( '%.2n', array_sum( $margins ) / count( $margins ) );
                    ?> avg
                </td>
                <td><?php
                    echo money_format( '%.2n', array_sum( $prices ) );
                    ?>
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
