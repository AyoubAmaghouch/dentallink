document.addEventListener('DOMContentLoaded',()=>{
    const toggle=document.querySelector('.mobile-toggle');
    const nav=document.querySelector('.navlinks');
    if(toggle&&nav){
        toggle.addEventListener('click',()=>{
            const open=nav.classList.toggle('open');
            toggle.setAttribute('aria-expanded',String(open));
        });
    }
    document.querySelectorAll('[data-copy]').forEach(button=>button.addEventListener('click',async()=>{
        const value=button.dataset.copy||'';
        try{
            if(navigator.clipboard&&window.isSecureContext){await navigator.clipboard.writeText(value);}
            else{const field=document.createElement('textarea');field.value=value;field.setAttribute('readonly','');field.style.position='fixed';field.style.opacity='0';document.body.appendChild(field);field.select();document.execCommand('copy');field.remove();}
            button.classList.add('is-copied');button.textContent='✓';button.setAttribute('aria-label','Copié');
            window.setTimeout(()=>{button.classList.remove('is-copied');button.innerHTML='<svg class="detail-svg" viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="13" height="13" rx="2"/><path d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"/></svg>';button.setAttribute('aria-label','Copier')},1400);
        }catch(error){button.setAttribute('aria-label','Copie impossible');}
    }));
document.querySelectorAll('.directory-card-photo[data-photos]').forEach(image=>{
        let photos=[];
        try{photos=JSON.parse(image.dataset.photos||'[]')}catch(error){return}
        if(photos.length<2)return;
        const card=image.closest('.lab-card');
        const dots=Array.from(card?.querySelectorAll('[data-gallery-index]')||[]);
        let index=0;
        const showPhoto=next=>{
            index=(next+photos.length)%photos.length;
            image.classList.add('is-changing');
            window.setTimeout(()=>{image.src=photos[index];image.classList.remove('is-changing')},120);
            dots.forEach((dot,dotIndex)=>dot.classList.toggle('active',dotIndex===index));
        };
        card?.querySelector('[data-gallery-prev]')?.addEventListener('click',event=>{event.preventDefault();event.stopPropagation();showPhoto(index-1)});
        card?.querySelector('[data-gallery-next]')?.addEventListener('click',event=>{event.preventDefault();event.stopPropagation();showPhoto(index+1)});
        dots.forEach((dot,dotIndex)=>dot.addEventListener('click',event=>{event.preventDefault();event.stopPropagation();showPhoto(dotIndex)}));
        window.setInterval(()=>showPhoto(index+1),1500);
    });
    const main=document.getElementById('gallery-main');
    const galleryButtons=Array.from(document.querySelectorAll('.thumb-button'));
    if(main&&galleryButtons.length){
        let activePhoto=Math.max(0,galleryButtons.findIndex(button=>button.querySelector('.thumb')?.classList.contains('active')));
        const showPhoto=index=>{
            activePhoto=(index+galleryButtons.length)%galleryButtons.length;
            const button=galleryButtons[activePhoto];
            main.classList.add('is-changing');
            window.setTimeout(()=>{main.src=button.dataset.src;main.classList.remove('is-changing')},110);
            galleryButtons.forEach((item,itemIndex)=>{
                const thumb=item.querySelector('.thumb');
                thumb?.classList.toggle('active',itemIndex===activePhoto);
                item.setAttribute('aria-current',itemIndex===activePhoto?'true':'false');
            });
        };
        galleryButtons.forEach((button,index)=>button.addEventListener('click',()=>showPhoto(index)));
        window.setInterval(()=>showPhoto(activePhoto+1),5000);
    }    const carousel=document.querySelector('[data-hero-carousel]');
    if(!carousel)return;
    const slots=Array.from(carousel.querySelectorAll('.hero-carousel-image'));
    const caption=carousel.closest('.hero-art')?.querySelector('[data-hero-caption]');
    const photos=[
        {src:'assets/images/img1.jpg',alt:'Couronnes dentaires sur un mod\u00e8le de laboratoire'},
        {src:'assets/images/img2.jpg',alt:'Professionnel manipulant une proth\u00e8se dentaire'},
        {src:'assets/images/img3.jpg',alt:'Dental technician using laboratory equipment'},
        {src:'assets/images/img4.jpg',alt:'Technician shaping a dental prosthesis'}
    ];
    const captions=['Un r\u00e9seau qui vous rapproche','Des laboratoires de confiance','L\u2019expertise dentaire au Maroc','Des solutions adapt\u00e9es \u00e0 vos besoins'];
    if(slots.length!==2)return;
    let activeSlot=0;
    let currentPhoto=0;
    let timer=0;
    let captionTimer=0;
    let paused=document.hidden;
    const motion=window.matchMedia('(prefers-reduced-motion: reduce)');
    const setCaption=index=>{
        if(!caption)return;
        caption.classList.add('is-changing');
        window.clearTimeout(captionTimer);
        captionTimer=window.setTimeout(()=>{
            caption.textContent=captions[index%captions.length];
            requestAnimationFrame(()=>caption.classList.remove('is-changing'));
        },motion.matches?0:140);
    };
    const preload=photo=>new Promise(resolve=>{
        const image=new Image();
        let settled=false;
        const finish=loaded=>{
            if(settled)return;
            settled=true;
            resolve(loaded);
        };
        image.decoding='async';
        image.onload=()=>finish(image.naturalWidth>0);
        image.onerror=()=>finish(false);
        image.src=photo.src;
        if(image.complete)finish(image.naturalWidth>0);
    });
    const schedule=delay=>{
        window.clearTimeout(timer);
        timer=paused?0:window.setTimeout(changePhoto,delay);
    };
    const changePhoto=async()=>{
        timer=0;
        if(paused||photos.length<2)return;
        const nextPhoto=(currentPhoto+1)%photos.length;
        const incomingSlot=(activeSlot+1)%2;
        const incoming=slots[incomingSlot];
        const outgoing=slots[activeSlot];
        incoming.classList.remove('is-active');
        incoming.src=photos[nextPhoto].src;
        incoming.alt=photos[nextPhoto].alt;
        try{
            if(typeof incoming.decode==='function')await incoming.decode();
            else if(!incoming.complete||incoming.naturalWidth===0)throw new Error('Image not ready');
        }catch(error){
            photos.splice(nextPhoto,1);
            if(photos.length>1){
                currentPhoto=photos.findIndex(photo=>photo.src===outgoing.getAttribute('src'));
                if(currentPhoto<0)currentPhoto=0;
                schedule(4000);
            }
            return;
        }
        if(paused)return;
        requestAnimationFrame(()=>requestAnimationFrame(()=>{
            incoming.classList.add('is-active');
            outgoing.classList.remove('is-active');
            setCaption(nextPhoto);
            const duration=motion.matches?180:900;
            window.setTimeout(()=>{
                activeSlot=incomingSlot;
                currentPhoto=nextPhoto;
                schedule(Math.max(0,4000-duration));
            },duration+40);
        }));
    };
    document.addEventListener('visibilitychange',()=>{
        paused=document.hidden;
        if(paused){window.clearTimeout(timer);timer=0;}
        else if(!timer)schedule(0);
    });
    setCaption(currentPhoto);
    Promise.all(photos.map(preload)).then(loaded=>{
        const available=photos.filter((photo,index)=>loaded[index]);
        if(available.length<2)return;
        photos.splice(0,photos.length,...available);
        const firstPhoto=photos.findIndex(photo=>photo.src===slots[activeSlot].getAttribute('src'));
        if(firstPhoto<0){
            currentPhoto=0;
            slots[activeSlot].src=photos[0].src;
            slots[activeSlot].alt=photos[0].alt;
        }else currentPhoto=firstPhoto;
        schedule(4000);
    });
});


document.addEventListener('DOMContentLoaded',()=>{
    const stage=document.querySelector('[data-home-video-stage]');
    if(!stage)return;
    const nav=stage.querySelector('.topbar');
    if(nav){
        let scrollFrame=0;
        const updateNav=()=>{
            scrollFrame=0;
            nav.classList.toggle('is-scrolled',window.scrollY>50);
        };
        const onScroll=()=>{
            if(!scrollFrame)scrollFrame=window.requestAnimationFrame(updateNav);
        };
        window.addEventListener('scroll',onScroll,{passive:true});
        updateNav();
    }
    const videos=Array.from(stage.querySelectorAll('.home-background-video'));
    if(videos.length!==2)return;
    videos.forEach(video=>{
        video.muted=true;
        video.defaultMuted=true;
        video.playsInline=true;
        video.autoplay=true;
        video.loop=false;
        video.preload='auto';
    });
    let active=-1;
    let request=0;
    let retryTimer=0;
    let hidden=document.hidden;
    const failed=new Set();
    const otherAvailable=index=>{
        const other=1-index;
        if(!failed.has(other))return other;
        if(!failed.has(index))return index;
        return -1;
    };
    const waitUntilPlayable=video=>new Promise(resolve=>{
        if(video.readyState>=HTMLMediaElement.HAVE_CURRENT_DATA){resolve(true);return;}
        let timer=0;
        const cleanup=ok=>{
            window.clearTimeout(timer);
            video.removeEventListener('loadeddata',onReady);
            video.removeEventListener('canplay',onReady);
            video.removeEventListener('error',onError);
            resolve(ok);
        };
        const onReady=()=>cleanup(true);
        const onError=()=>cleanup(false);
        video.addEventListener('loadeddata',onReady,{once:true});
        video.addEventListener('canplay',onReady,{once:true});
        video.addEventListener('error',onError,{once:true});
        timer=window.setTimeout(()=>cleanup(false),10000);
        if(video.networkState===HTMLMediaElement.NETWORK_EMPTY)video.load();
    });
    const retryAvailable=()=>{
        window.clearTimeout(retryTimer);
        retryTimer=window.setTimeout(()=>{
            failed.clear();
            start(active<0?0:active);
        },1800);
    };
    const start=async requestedIndex=>{
        if(hidden)return;
        let index=requestedIndex;
        if(failed.has(index))index=otherAvailable(index);
        if(index<0){retryAvailable();return;}
        const token=++request;
        const video=videos[index];
        const ready=await waitUntilPlayable(video);
        if(token!==request||hidden)return;
        if(!ready){
            failed.add(index);
            const fallback=otherAvailable(index);
            if(fallback>=0)start(fallback);else retryAvailable();
            return;
        }
        try{
            if(video.ended||video.currentTime>=video.duration)video.currentTime=0;
            await video.play();
            if(token!==request||hidden)return;
            active=index;
            videos.forEach((item,i)=>item.classList.toggle('is-visible',i===index));
            videos.forEach((item,i)=>{if(i!==index)item.pause();});
        }catch(error){
            failed.add(index);
            const fallback=otherAvailable(index);
            if(fallback>=0)start(fallback);else retryAvailable();
        }
    };
    videos.forEach((video,index)=>{
        video.addEventListener('loadeddata',()=>{
            if(active<0&&index===0)start(0);
        });
        video.addEventListener('ended',()=>{
            if(active===index)start(1-index);
        });
        video.addEventListener('error',()=>{
            failed.add(index);
            if(active===index){
                const fallback=otherAvailable(index);
                if(fallback>=0)start(fallback);else retryAvailable();
            }else if(active<0&&index===0){
                const fallback=otherAvailable(index);
                if(fallback>=0)start(fallback);else retryAvailable();
            }
        });
    });
    document.addEventListener('visibilitychange',()=>{
        hidden=document.hidden;
        if(hidden)videos.forEach(video=>video.pause());
        else start(active<0?0:active);
    });
    start(0);
});
