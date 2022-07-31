<?php
class Locality_RU {

  public function num_ending($num) {

    switch ((int)$num%10) {
      case 2:
      case 3:
      case 4:
        return 'a';
      case 5:
      case 6:
      case 7:
      case 8:
      case 9:
      case 0:
        return 'ов';
    }
	
	}

  public function days($days) {
  
		switch (substr((int)$days, -1)) {
			case 1:
				return 'день';
			case 2:
			case 3:
			case 4:
				return 'дня';
			default:
				return 'дней';
		}

  }

}
