/*
 * smixer - a simple interface to /dev/mixer
 * Copyright (C) 2000 David Johnson
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * or view it online at http://www.gnu.org/copyleft/gpl.html
 * 
 */

#include <stdio.h>
#include <sys/ioctl.h>
#include <sys/soundcard.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <string.h>
#include <unistd.h>
#include <stdlib.h>

/* defines */
#define VERSION       "v1.0.1"
#define VERSION_DATE  "8/19/00"

#define SHOW_ALL_DEVS 0
#define VERBOSE       0

#define MAX_VOL       25700
#define MIXER_DEV     "/dev/mixer"
#define STR_SIZE      100


#define max2(a,b) ( ((a) > (b)) ? (a) : (b) )
#define min2(a,b) ( ((a) < (b)) ? (a) : (b) )
#define max3(a,b,c) ( max2((a),max2((b),(c))) )
#define min3(a,b,c) ( min2((a),min2((b),(c))) )
#define between(min,a,max) ( max2(min2((a),(max)),(min)) )



/* globals */
const char *mixer_dev_names_const[] = SOUND_DEVICE_LABELS;
char mixer_dev_names[SOUND_MIXER_NRDEVICES][11];

/* structs */
typedef struct {
  int devmask, recsrc, recmask, stereo, vols[SOUND_MIXER_NRDEVICES];
} mixer_data_t;

/* functions */
void removeSpacesAndCaps (void);
void readMixer (void);
void writeMixer (char *filename);
int getData (int fd, mixer_data_t *data);
void printData (mixer_data_t *data);
char* nocaps(char *text);
void printHelp(void);

/* main */

int main ( int argc, char **argv ) {
  
  removeSpacesAndCaps();
  
  if ( argc == 2 && !strcmp(argv[1],"-p") )
    readMixer();
  else if ( argc == 3 && !strcmp(argv[1],"-s") )
    writeMixer(argv[2]);
  else
    {
      fprintf(stderr,
	      "smixer " VERSION " (" VERSION_DATE ") Copyright (C) 2000 David Johnson\n"
	      "  http://centerclick.org/programs/smixer/\n"
	      "  smixer@centerclick.org\n"
	      "usage: %s -p          (print settings)\n"
	      "usage: %s -s [file|-] (set settings from file or stdin)\n",
	      argv[0],argv[0]);
      return (1);
    }
  
  return (0);
}


void readMixer (void) {
  int mixer_fd;
  mixer_data_t data;
  
  if ( ( mixer_fd = open(MIXER_DEV,O_RDWR) ) < 0 )
    {
      perror("open " MIXER_DEV);
      goto readMixerCleanup;
    }
  
  if ( getData(mixer_fd,&data) < 0 )
      goto readMixerCleanup;
  
  printData(&data);
  
 readMixerCleanup:
  
  if (mixer_fd > 2) close(mixer_fd);
  
  return;
  
}

void writeMixer (char *filename) {
  int mixer_fd=0, i, num_args, found;
  FILE *config_fd=NULL;
  mixer_data_t data;
  char templine[STR_SIZE];
  char arg1[STR_SIZE], arg2[STR_SIZE], arg3[STR_SIZE];
  int vol1, vol2;

  if ( !strcmp(filename,"-") )
    {
      config_fd = stdin;
    }
  else
    {
      if ( ( config_fd = fopen(filename,"r") ) == NULL )
	{
	  perror("open config");
	  goto writeMixerCleanup;
	}
    }

  fflush(stdout);
  
  if ( ( mixer_fd = open(MIXER_DEV,O_RDWR) ) < 0 )
    {
      perror("open " MIXER_DEV);
      goto writeMixerCleanup;
    }
  
  if ( getData(mixer_fd,&data) < 0 )
      goto writeMixerCleanup;
  
  
  while ( fgets(templine,STR_SIZE,config_fd) )
    {
      
      bzero(arg1,STR_SIZE);
      bzero(arg2,STR_SIZE);
      vol1 = -1;
      vol2 = -1;
      
      num_args = sscanf(templine,"%s %s %s\n",arg1,arg2,arg3);
      
      if ( num_args == 0 || arg1[0] == '\0' || arg1[0] == '#' )
	continue;
      
      if ( !strcmp(arg1,"vol") )
	{
	  found=0;
	  nocaps(arg2);
	  
	  if (num_args < 3)
	    {
	      fprintf(stderr,"vol: missing argument\n");
	      continue;
	    }
	  
	  for (i=0; i<SOUND_MIXER_NRDEVICES; i++)
	    if ( data.devmask & (1 << i) && !strcmp(arg2,mixer_dev_names[i]) )
	      {
		found++;
		
		if ( !strcmp(arg3,"+") ) /* + 5 % */
		  vol1 = between(0, data.vols[i] + (5*MAX_VOL/100), MAX_VOL);
		else if ( !strcmp(arg3,"-") ) /* - 5 % */
		  vol1 = between(0, data.vols[i] - (5*MAX_VOL/100), MAX_VOL);
		else
		  vol1 = (int)((float)MAX_VOL * (float)atoi(arg3) / 100.0);
		
		if (vol1 < 0 || vol1 > MAX_VOL)
		  {
		    fprintf(stderr,"vol: value out of range\n");
		    continue;
		  }
		
		if ( ioctl(mixer_fd,MIXER_WRITE(i),&vol1) < 0 )
		  {
		    perror("write vol");
		    goto writeMixerCleanup;
		  }
		if ( ioctl(mixer_fd,MIXER_READ(i),&vol2) < 0 )
		  {
		    perror("comfirm vol");
		    goto writeMixerCleanup;
		  }
		data.vols[i] = vol2;
		if ( vol1 != vol2 )
		  {
		    fprintf(stderr,
			    "comparision failed: tried %i got %i\n",
			    vol1,vol2);
		    goto writeMixerCleanup;
		  }
	      }
	  
	  if (!found)
	    {
	      fprintf(stderr,"vol: name not found: %s\n",arg2);
	    }
	}
      
      else if ( !strcmp(arg1,"recsrc") )
	{
	  found=0;
	  nocaps(arg2);
	  
	  for (i=0; i<SOUND_MIXER_NRDEVICES; i++)
	    if ( data.recmask & (1 << i) && !strcmp(arg2,mixer_dev_names[i]) )
	      {
		found++;
		vol1 = (1 << i);
		
		if ( ioctl(mixer_fd,SOUND_MIXER_WRITE_RECSRC,&vol1) < 0 )
		  {
		    perror("write vol");
		    goto writeMixerCleanup;
		  }
		if ( ioctl(mixer_fd,SOUND_MIXER_READ_RECSRC,&vol2) < 0 )
		  {
		    perror("comfirm vol");
		    goto writeMixerCleanup;
		  }
		data.recsrc = vol2;
		if ( vol1 != vol2 )
		    fprintf(stderr,
			    "comparision failed: tried 0x%.8X got 0x%.8X\n",
			    vol1,vol2);
		
		
	      }
	  
	  if (!found)
	    fprintf(stderr,"recsrc: name not found: %s\n",arg2);
	  
	}
	
      else if ( !strcmp(arg1,"show") )
	  printData(&data);
      
      else if ( !strcmp(arg1,"help") ||
		!strcmp(arg1,"?") )
	  printHelp();
      
      else if ( !strcmp(arg1,"end") ||
		!strcmp(arg1,"exit") ||
		!strcmp(arg1,"q") ||
		!strcmp(arg1,"quit"))
	  goto writeMixerCleanup;
      
      else
	fprintf(stderr,"unknown command try \"help\": %s\n",arg1);
      
      
    }
  
 writeMixerCleanup:
  
  if (mixer_fd > 2) close(mixer_fd);
  if (config_fd) fclose(config_fd);
  
  return;
  
}

int getData (int fd, mixer_data_t *data) {
  int i;
  
  bzero(data,sizeof(mixer_data_t));
  
  if ( ioctl(fd,SOUND_MIXER_READ_DEVMASK,&data->devmask) < 0 )
    {
      perror("read SOUND_MIXER_DEVMASK");
      return -1;
    }
  
  if ( ioctl(fd,SOUND_MIXER_READ_RECSRC,&data->recsrc) < 0 )
    {
      perror("read SOUND_MIXER_RECSRC");
      return -1;
    }
  
  if ( ioctl(fd,SOUND_MIXER_READ_RECMASK,&data->recmask) < 0 )
    {
      perror("read SOUND_MIXER_RECMASK");
      return -1;
    }
  
  if ( ioctl(fd,SOUND_MIXER_READ_STEREODEVS,&data->stereo) < 0 )
    {
      perror("read SOUND_MIXER_STEREODEVS");
      return -1;
    }
  
  
  for (i=0; i<SOUND_MIXER_NRDEVICES; i++)
    if ( data->devmask & ( 1 << i) )
      if ( ioctl(fd,MIXER_READ(i),&data->vols[i]) < 0 )
	{
	  perror("read SOUND_MIXER_STEREODEVS");
	  return -1;
	}
  
  return 0;
  
}

void printData (mixer_data_t *data)
{
  int i;
  
  for (i=0; i<SOUND_MIXER_NRDEVICES; i++) {
    if ( (data->devmask | data->recmask) & (1 << i) )
      printf("%-8s%3.0f\n",
	     mixer_dev_names_const[i],
	     100.0*((float)data->vols[i])/((float)MAX_VOL)
	     );
   
  }
  
  
}


void printHelp(void)
{
  
  printf("\nsmixer commands:\n"

	 "\nvol [name] [value|-|+]\n"
	 "  vol sets the volume for a specific input or output device\n"
	 "  name is the name of the device, do smixer -p to get a list\n"
	 "  value is the percentage volume (no %%)\n"
	 "    or \"-\" to decrease 5%% or \"+\" to increase 5%%\n"
	 
	 "\nrecsrc [name]\n"
	 "  sets the recording source to a specific input device\n"
	 "  name is the name of the device, do smixer -p to get a list\n"
	 
	 "\nshow\n"
	 "  prints the list that smixer -p does\n"

	 "\nhelp|?\n"
	 "  gets help\n"

	 "\nend|exit|quit|q\n"
	 "  exits\n"

	 );
  
}


void removeSpacesAndCaps (void) {
  int i,j;
  
  for (i=0; i<SOUND_MIXER_NRDEVICES; i++)
    {
      strncpy(mixer_dev_names[i],mixer_dev_names_const[i],11);
      nocaps(mixer_dev_names[i]);
      for (j=0; mixer_dev_names[i][j] != '\0'; j++)
	if ( mixer_dev_names[i][j] == ' ')
	  mixer_dev_names[i][j] = '\0';
    }
  
}

char* nocaps(char *text)
{
  int i;
  if (text)
    for (i=0; i<strlen(text); i++)
      if ( text[i] >= 'A' && text[i] <= 'Z' )
	text[i] = text[i]+32;
  return text;
}
