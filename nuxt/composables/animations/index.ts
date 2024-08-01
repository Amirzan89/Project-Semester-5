import { ref } from 'vue';
import { getGsap } from '../config';
import { header } from './header';
import { footer } from './footer';
export default () => {
    const gsap = getGsap();
    const gsapContext: Ref = ref(null);
    const isAnimateComplete = ref(false);
    gsapContext.value =  gsap.context(() => {
        const rootTl = gsap.timeline({
            onComplete: () => {
                isAnimateComplete.value = true;
            }
        });
        rootTl.add(header(), 0);

        const tl1 = gsap.timeline();
        const secMe = gsap.utils.selector('section#me div');
        tl1.from(secMe('h3'), {
            x:'-100%',
            opacity: 0,
            delay: 0.6,
            duration: 1,
        }, 0);
        tl1.from(secMe('h1'), {
            x:'-50%',
            opacity: 0,
            delay: 0.7,
            duration: 1,
        }, 0);
        tl1.from(secMe('a:not(#btnContact)'), {
            y:'200%',
            opacity: 0,
            delay: 1,
            duration: 1,
            stagger: {
                from: 'start',
                each: 0.3,
            },
        }, 0);
        tl1.from(secMe('a#btnContact'), {
            y:'300%',
            opacity: 0,
            delay: 0.8,
            duration: 1,
        }, 0);
        tl1.from(secMe('img'), {
            x:'100%',
            delay: 0.65,
            opacity: 0,
            duration: 1,
        }, 0);
        rootTl.add(tl1, 0)

        const secAbout = gsap.utils.selector('section#about');
        const tl2 = gsap.timeline({
            scrollTrigger: {
                trigger: 'section#about',
                start: 'top 40%',
                end: 'top none',
            }
        });
        tl2.from(secAbout('p'), {
            x:'-100%',
            opacity: 0,
            stagger: {
                from: 'random',
                each: 0.3,
            },
        }, 0)
        tl2.from(secAbout('a:not(#btnCV)'), {
            y:'-100%',
            opacity: 0,
            stagger: {
                from: 'random',
                each: 0.27,
            },
        }, '+=0.15')
        tl2.from(secAbout('a#btnCV'), {
            y:'200%',
            opacity: 0,
            duration: 1,
        }, '-=1.8')

        const secProject = gsap.utils.selector('section#project');
        const tl3 = gsap.timeline({
            scrollTrigger: {
                trigger: 'section#project',
                start: 'top 60%',
                end: 'top none',
            }
        });
        tl3.from(secProject('span'), {
            x:'-100%',
            opacity: 0,
            duration: 1.3,
        }, 0);
        tl3.from(secProject('a'), {
            x:'100%',
            opacity: 0,
            duration: 1.3,
        }, 0);

        const secContact = gsap.utils.selector('section#contact');
        const tl4 = gsap.timeline({
            scrollTrigger: {
                trigger: 'section#contact',
                start: '20% 35%',
                end: 'top none',
            }
        });
        tl4.from(secContact('div'), {
            y:'-15%',
            opacity: 0,
            duration: 1.2,
        }, 0);

        footer();
        return rootTl;
    });
    return { gsapContext, isAnimateComplete }
}