CREATE TABLE `articlealert` (
  `alertid` int(50) NOT NULL,
  `feeddataid` int(50) NOT NULL,
  `userId` int(50) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `articlealert`
  ADD PRIMARY KEY (`alertid`);
ALTER TABLE `articlealert`
  MODIFY `alertid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;