[![Current version](https://img.shields.io/packagist/v/maatify/queue-manager)][pkg]
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/maatify/queue-manager)][pkg]
[![Monthly Downloads](https://img.shields.io/packagist/dm/maatify/queue-manager)][pkg-stats]
[![Total Downloads](https://img.shields.io/packagist/dt/maatify/queue-manager)][pkg-stats]
[![Stars](https://img.shields.io/packagist/stars/maatify/queue-manager)](https://github.com/maatify/QueueManager/stargazers)

[pkg]: <https://packagist.org/packages/maatify/queue-manager>
[pkg-stats]: <https://packagist.org/packages/maatify/routee/queue-manager>
# Installation

```shell
composer require maatify/queue-manager
```

## Database Structure
```mysql

--
-- Database: `maatify`
--

-- --------------------------------------------------------

--
-- Table structure for table `queue_manager`
--

CREATE TABLE `queue_manager` (
     `queue_id` int NOT NULL,
     `description` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
     `timestamp` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue_manager`
--

INSERT INTO `queue_manager` (`queue_id`, `description`, `timestamp`) 
    VALUES
        (1, 'email', ''),
        (2, 'sms', ''),
        (3, 'fcm', ''),
        (4, 'payment', ''),
        (5, 'order', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `queue_manager`
--
ALTER TABLE `queue_manager`
    ADD PRIMARY KEY (`queue_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `queue_manager`
--
ALTER TABLE `queue_manager`
    MODIFY `queue_id` int NOT NULL AUTO_INCREMENT;
COMMIT;
```