#include <idc.idc>
#define PT_LOAD              1
#define PT_DYNAMIC           2
static main(void)
{
	auto ImageBase,StartImg,EndImg;
	auto e_phoff;
	auto e_phnum,p_offset;
	auto i,dumpfile;
	ImageBase=0x400000;
	StartImg=0x400000;
	EndImg=0x0;
	if (Dword(ImageBase)==0x7f454c46 || Dword(ImageBase)==0x464c457f )
 	{
		if(dumpfile=fopen("C:\\dumpfile","wb"))
		{
			e_phoff=ImageBase+Qword(ImageBase+0x20);
			Message("e_phoff = 0x%x\n", e_phoff);
			e_phnum=Word(ImageBase+0x38);
			Message("e_phnum = 0x%x\n", e_phnum);
			for(i=0;i<e_phnum;i++)
			{
				if (Dword(e_phoff)==PT_LOAD || Dword(e_phoff)==PT_DYNAMIC)
				{ 
						p_offset=Qword(e_phoff+0x8);
						StartImg=Qword(e_phoff+0x10);
						EndImg=StartImg+Qword(e_phoff+0x28);
						Message("start = 0x%x, end = 0x%x, offset = 0x%x\n", StartImg, EndImg, p_offset);
						dump(dumpfile,StartImg,EndImg,p_offset);
						Message("dump segment %d ok.\n",i);
				}    
				e_phoff=e_phoff+0x38;
			}

			fseek(dumpfile,0x3c,0);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);

			fseek(dumpfile,0x28,0);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);
			fputc(0x00,dumpfile);

			fclose(dumpfile);
        }
		else Message("dump err.");
 	}
}

static dump(dumpfile,startimg,endimg,offset) 
{
	auto i;
	auto size;
	size=endimg-startimg;
	fseek(dumpfile,offset,0);
	for ( i=0; i < size; i=i+1 ) 
	{
		fputc(Byte(startimg+i),dumpfile);
	}
}
